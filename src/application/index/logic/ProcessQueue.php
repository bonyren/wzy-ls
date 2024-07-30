<?php
// +----------------------------------------------------------------------
// | WZYCODING [ SIMPLE SOFTWARE IS THE BEST ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2025 wzycoding All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://license.coscl.org.cn/MulanPSL2 )
// +----------------------------------------------------------------------
// | Author: wzycoding <wzycoding@qq.com>
// +----------------------------------------------------------------------
namespace app\index\logic;
use think\Log;
use think\Debug;
use think\Db;
use app\index\Defs as IndexDefs;
use app\index\logic\Subscribers as SubscribersLogic;
use app\index\logic\Campaigns as CampaignsLogic;
use app\index\mail\CampaignEmail;
use app\index\mail\SystemEmail;
use app\index\service\EventLogs as EventLogsService;

class ProcessQueue extends Base{
    public function __construct(){}
    public function process(){
        //刷新订阅者
        $subscribersLogic = SubscribersLogic::I();
        $subscribersLogic->refreshAllSubscriberUniqid();
        $subscribersLogic->refreshAllSubscriberUuid();
        //刷新投递活动
        $campaignsLogic = CampaignsLogic::I();
        $campaignsLogic->refreshAllCampaignUuid();
        //一段时间发送的最大数量，30秒内最大发送1000封，包括所有的campaigns
        /*
        $num_per_batch = 1000;
        $batch_period = 30;//seconds*/
        //5分钟发送一封邮件
        $num_per_batch = 1;
        $batch_period = 300;//seconds

        // check how many were sent in the last batch period and take off that
        if($num_per_batch && $batch_period){
            $recentlySentCount = Db::table('campaign_subscribers')->where([
                'entered'=>['exp', Db::raw(">date_sub(now(),interval $batch_period second)")],
                'status'=>IndexDefs::eCampaignSubscriberStatusSentSuccess
            ])->count();
            $num_per_batch -= $recentlySentCount;
            if($num_per_batch < 0){
                $num_per_batch = 0;
            }
        }
        if($num_per_batch == 0){
            Log::notice("ProcessQueue, the current batch run out, batch num: $num_per_batch, batch period: $batch_period");
            return;
        }
        Log::notice("ProcessQueue, num per batch: $num_per_batch");
        ////////////////////////////////////////////////////////////////////////////
        //get the submitted, in progress campaigns
        //获取处于“已提交”，“投递中”的活动
        $campaignLimit = 5;
        $campaignIds = Db::table('campaigns')
            ->where([
            'status'=>['not in', [IndexDefs::eCampaignStatusDraft, IndexDefs::eCampaignStatusSent, IndexDefs::eCampaignStatusSuspended]],
            'embargo'=>['exp', Db::raw('<now()')]
        ])->limit($campaignLimit)->order('entered')->column('campaign_id');
        Log::notice("ProcessQueue, process campaigns count: " . count($campaignIds));
        if(empty($campaignIds)){
            Log::notice("ProcessQueue, no campaign to process");
            return;
        }
        //处理投递活动
        $counters = [];
        $counters['success_sent'] = 0;
        $counters['not_sent'] = 0;
        $counters['failed_sent'] = 0;

        $campaignEmailLogic = new CampaignEmail();
        foreach($campaignIds as $campaignId){
            Log::notice("ProcessQueue, process campaign: $campaignId");
            //判断是否达到了本次投递的数量限制
            if ($num_per_batch && $counters['success_sent'] >= $num_per_batch) {
                Log::notice("batch limit reached : " . $counters['success_sent'] . " ($num_per_batch)");
                break;
            }

            $counters['total_users_for_message ' . $campaignId] = 0;
            $counters['processed_users_for_message ' . $campaignId] = 0;
            //proccessed = not send + sent + fail
            $counters['not_send_users_for_message ' . $campaignId] = 0;
            $counters['sent_users_for_message ' . $campaignId] = 0;
            $counters['failed_sent_for_message ' . $campaignId] = 0;

            //////////////////////////////////////////////////////////
            //更新“已提交”->“投递中”
            Db::table('campaigns')
                ->where(['campaign_id'=>$campaignId, 'status'=>IndexDefs::eCampaignStatusSubmitted])
                ->update(
                    [
                        'status'=>IndexDefs::eCampaignStatusInprogress,
                        'send_start_time'=>date('Y-m-d H:i:s')
                    ]
                );

            $campaignData = $campaignsLogic->loadCampaignData($campaignId);
            if(empty($campaignData)){
                Log::notice("ProcessQueue, failed to load campaign data fro {$campaignId}");
                continue;
            }
            //send start notifications
            $this->sendStartAlert($campaignId, $campaignData['subject'], $campaignData['send_start_alert']);
            //# check the end date of the campaign
            $stopSending = false;
            if(!empty($campaignData['stop_after'])){
                $secondsTogo = strtotime($campaignData['stop_after']) - time();
                $stopSending = $secondsTogo < 0;
            }
            if($stopSending){
                //超出了发送时间的有效性
                Log::notice("ProcessQueue, sending of $campaignId campaign stop, stop after: " . $campaignData['stop_after']);
                goto CAMPAIGN_EN_NOTIFICATION;
            }
            //preCacheCampaign?
            if(!$campaignsLogic->preCacheCampaign($campaignId)){
                //更新活动为“中止”状态
                Db::table('campaigns')->where('campaign_id', $campaignId)->update(['status'=>IndexDefs::eCampaignStatusSuspended]);
                logEvent("投递活动'{$campaignData['subject']}'由于加载数据失败被中止", EventLogsService::eSeverityWarning);
                Log::error("ProcessQueue, Error loading campaign $campaignId");
                continue;
            }
            //load the subscribers under the campaign
            //查询所有在该campaign下有小的subscribers, 必须不在黑名单
            //先清理
            Db::table('campaign_subscribers')
                ->where(['campaign_id'=>$campaignId, 'status'=>IndexDefs::eCampaignSubscriberStatusSubmitted])
                ->delete();
            //一次性load所有的subscribers, 对内存的冲击?
            $subscriberIds = Db::table('list_subscribers')->alias('LS')
                ->join('subscribers S', 'LS.subscriber_id=S.subscriber_id')
                ->join('campaigns_lists CL', "LS.list_id=CL.list_id and CL.campaign_id={$campaignId}")
                ->where(['S.blacklisted'=>0, 'S.deleted'=>0])
                ->distinct(true)
                ->column('S.subscriber_id');
            $subscriberQueueSize = count($subscriberIds);
            if($subscriberQueueSize == 0){
                Log::notice("can't find subscribers under campaign $campaignId");
                goto CAMPAIGN_EN_NOTIFICATION;
            }
            Log::notice("ProcessQueue, campaign: $campaignId, subscriber count: $subscriberQueueSize");
            //全部subscribers，包括没发送的，发送过的
            $counters['total_users_for_message ' . $campaignId] = $subscriberQueueSize;
            $campaignsLogic->setCampaignData($campaignId, 'to process', $subscriberQueueSize);

            foreach($subscriberIds as $subscriberId){
                //判断是否达到了本次投递的数量限制
                if ($num_per_batch && $counters['success_sent'] >= $num_per_batch) {
                    Log::notice("batch limit reached : " . $counters['success_sent'] . " ($num_per_batch)");
                    break;
                }
                Log::notice("ProcessQueue, campaign: $campaignId, subscriber: $subscriberId");
                ++$counters['processed_users_for_message ' . $campaignId];

                Db::table('campaigns')->where('campaign_id', $campaignId)->setInc('processed_count');
                //skip the subscriber status is 'sent'
                //已经投递过
                $bExist = Db::table('campaign_subscribers')->where(['campaign_id'=>$campaignId, 'subscriber_id'=>$subscriberId, 'status'=>IndexDefs::eCampaignSubscriberStatusSentSuccess])->count();
                if($bExist){
                    //the campaign has ben sent for the subscriber, so skip it.
                    //已经投递过
                    ++$counters['not_sent'];
                    ++$counters['not_send_users_for_message ' . $campaignId];
                    continue;
                }
                //update all the subscriber status to 'submitted'
                //更新为‘已提交’
                $existCount = Db::table('campaign_subscribers')->where(['campaign_id'=>$campaignId, 'subscriber_id'=>$subscriberId])->count();
                if($existCount) {
                    Db::table('campaign_subscribers')->where(['campaign_id' => $campaignId, 'subscriber_id' => $subscriberId])->update(['status' => IndexDefs::eCampaignSubscriberStatusSubmitted]);
                }else{
                    Db::table('campaign_subscribers')->insert([
                        'campaign_id'=>$campaignId,
                        'subscriber_id'=>$subscriberId,
                        'status'=>IndexDefs::eCampaignSubscriberStatusSubmitted
                    ]);
                }
                //获取订阅者数据
                $subscriberData = $subscribersLogic->getSubscriber($subscriberId);
                if(!$subscriberData){
                    Log::error("failed to get subscriber data for $subscriberId");
                    ++$counters['not_sent'];
                    ++$counters['not_send_users_for_message ' . $campaignId];
                    continue;
                }
                $subscriberEmail = $subscriberData['email'];
                //更新为发送中
                Db::table('campaign_subscribers')->where(['campaign_id'=>$campaignId, 'subscriber_id'=>$subscriberId])->update(['status'=>IndexDefs::eCampaignSubscriberStatusInprogress]);

                Log::notice("ProcessQueue, start sendCampaignMail campaign: $campaignId, subscriber: $subscriberEmail");

                try {
                    $success = $campaignEmailLogic->sendCampaignMail($campaignId, $subscriberEmail);
                    if ($success) {
                        ++$counters['success_sent'];
                        ++$counters['sent_users_for_message ' . $campaignId];
                        Db::table('campaign_subscribers')->where(['campaign_id' => $campaignId, 'subscriber_id' => $subscriberId])->update(['status' => IndexDefs::eCampaignSubscriberStatusSentSuccess]);
                    } else {
                        ++$counters['failed_sent'];
                        ++$counters['failed_sent_for_message ' . $campaignId];
                        Db::table('campaign_subscribers')->where(['campaign_id' => $campaignId, 'subscriber_id' => $subscriberId])->update(['status' => IndexDefs::eCampaignSubscriberStatusSentFail]);
                        logEvent("发送邮件失败 '{$campaignData['subject']}' to $subscriberEmail");
                    }
                }catch (\Exception $e){
                    Log::notice("ProcessQueue, sendCampaignMail exception: " . $e->getMessage());

                    ++$counters['failed_sent'];
                    ++$counters['failed_sent_for_message ' . $campaignId];
                    Db::table('campaign_subscribers')->where(['campaign_id' => $campaignId, 'subscriber_id' => $subscriberId])->update(['status' => IndexDefs::eCampaignSubscriberStatusSentFail]);
                    logEvent("发送邮件异常 '{$campaignData['subject']}' to $subscriberEmail, exception: " . $e->getMessage());
                }
                Log::notice("ProcessQueue, end sendCampaignMail campaign: $campaignId, subscriber: $subscriberEmail");
            }
CAMPAIGN_EN_NOTIFICATION:
            if($stopSending){
                //stop, update to sent
                Db::table('campaigns')->where('campaign_id', $campaignId)->update(['status' => IndexDefs::eCampaignStatusSent, 'send_end_time' => date('Y-m-d H:i:s')]);
            }else if ($counters['total_users_for_message ' . $campaignId] - $counters['processed_users_for_message ' . $campaignId] <= 0) {
                // this campaign is done
                if ($counters['failed_sent_for_message ' . $campaignId] == 0) {
                    if($campaignData['requeue_interval'] == IndexDefs::eRequeueIntervalNot){
                        //send over
                        Db::table('campaigns')->where('campaign_id', $campaignId)->update(['status' => IndexDefs::eCampaignStatusSent, 'send_end_time' => date('Y-m-d H:i:s')]);
                    }else{
                        //requeue for the next send
                        $nextEmbargoRow = Db::table('campaigns')->where('campaign_id', $campaignId)
                            ->field(['embargo + INTERVAL (FLOOR(TIMESTAMPDIFF(MINUTE, embargo, NOW()) / requeue_interval) + 1) * requeue_interval MINUTE'=>'next_embargo'])
                            ->find();
                        if(!$nextEmbargoRow){
                            return;
                        }
                        $nextEmbargo = $nextEmbargoRow['next_embargo'];
                        if($campaignData['stop_after'] > $nextEmbargo) {
                            //等待下一次投递
                            Db::table('campaigns')->where(['campaign_id' => $campaignId, 'embargo' => ['exp', Db::raw('<now()')]])
                                ->update([
                                'status' => IndexDefs::eCampaignStatusSubmitted,
                                'embargo' => Db::raw('embargo + INTERVAL (FLOOR(TIMESTAMPDIFF(MINUTE, embargo, NOW()) / requeue_interval) + 1) * requeue_interval MINUTE')
                            ]);
                        }else{
                            //投递完成
                            Db::table('campaigns')->where('campaign_id', $campaignId)->update(['status' => IndexDefs::eCampaignStatusSent, 'send_end_time' => date('Y-m-d H:i:s')]);
                        }
                    }
                    //send end notifications
                    $this->sendEndAlert($campaignId, $campaignData['subject'], $campaignData['send_end_alert']);
                }else{
                    //如果有发送失败，则继续尝试发送,直到最后的结束时间
                }
            }else{
                //do nothing
            }
        }
    }

    public function sendStartAlert($campaignId, $campaignSubject, $alertEmails){
        //adminMessage('投递开始', 'CamelList开始投递邮件，主题： ' . $campaignSubject);
        if(empty($alertEmails)){
            return;
        }
        $campaignsLogic = CampaignsLogic::I();
        $systemEmailLogic = new SystemEmail();

        $notifications = explode(",",  $alertEmails);
        foreach($notifications as $notification){
            $sendResult = $systemEmailLogic->sendDirectEmail($notification, '投递开始',
                'CamelList开始投递邮件，主题： ' . $campaignSubject . "\n\n"
            );
            if(!$sendResult){
                Log::error("ProcessQueue, failed to send email notification: $notification, campaign: $campaignId");
            }
        }
        $campaignsLogic->setCampaignData($campaignId, 'start_notified', date('Y-m-d H:i:s'));
    }
    public function sendEndAlert($campaignId, $campaignSubject, $alertEmails){
        //adminMessage('投递结束', 'CamelList结束投递邮件，主题： ' . $campaignSubject);
        if(empty($alertEmails)){
            return;
        }
        $campaignsLogic = CampaignsLogic::I();
        $systemEmailLogic = new SystemEmail();

        $notifications = explode(",",  $alertEmails);
        foreach($notifications as $notification){
            $sendResult = $systemEmailLogic->sendDirectEmail(
                $notification, '投递结束', 'CamelList结束投递邮件，主题： ' . $campaignSubject . "\n\n"
            );
            if(!$sendResult){
                Log::error("ProcessQueue, failed to send email notification: $notification, campaign: $campaignId");
            }
        }
        $campaignsLogic->setCampaignData($campaignId, 'end_notified', date('Y-m-d H:i:s'));
    }
}