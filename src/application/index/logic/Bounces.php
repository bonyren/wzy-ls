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
use think\Request;
use app\index\logic\Subscribers as SubscribersLogic;
use app\index\Defs as IndexDefs;

class Bounces extends Base{
    public function __construct(){
        parent::__construct();
    }

    /**已处理
     * @param array $search
     * @param int $page
     * @param int $rows
     * @param string $sort
     * @param string $order
     * @return array
     * @throws \think\Exception
     */
    public function loadProcessedBounces($search=array(),
                                         $page=1,
                                         $rows=DEFAULT_PAGE_ROWS,
                                         $sort = '',
                                         $order = ''){
            /////////////////////////////////////////////////
            $limit = ($page - 1) * $rows . "," . $rows;
            $order = 'bounce_id desc';
            /////////////////////////////////////////////////
            $conditions = [];
            $conditions['processed_status'] = ['<>', IndexDefs::eBounceUnidentified];

            if(!emptyInArray($search, 'mailbox')){
                $conditions['mailbox_id'] = $search['mailbox'];
            }
            $totalCount = Db::table('bounces')->where($conditions)->count();
            $records = Db::table('bounces')->where($conditions)
                ->limit($limit)
                ->order($order)
                ->field('bounce_id, date, processed_status, comment')
                ->select();
            foreach($records as &$record){
                $bounceId = $record['bounce_id'];
                $count = Db::table('bounces_campaign_subscriber')->where('bounce_id', $bounceId)->count();
                if($count) {
                    $bounceCampaignSubscriber = Db::table('bounces_campaign_subscriber')->alias('BCS')
                        ->join('campaigns C', 'BCS.campaign_id=C.campaign_id', 'LEFT')
                        ->join('subscribers S', 'BCS.subscriber_id=S.subscriber_id', 'LEFT')
                        ->where('BCS.bounce_id', $bounceId)
                        ->field('C.subject, S.email')
                        ->find();
                    //$bounceCampaignSubscriber must not be null
                    if($bounceCampaignSubscriber['subject']){
                        $record['campaign_subject'] = $bounceCampaignSubscriber['subject'];
                    }else{
                        $record['campaign_subject'] = "未知";
                    }
                    if($bounceCampaignSubscriber['email']){
                        $record['subscriber_email'] = $bounceCampaignSubscriber['email'];
                    }else{
                        $record['subscriber_email'] = "未知";
                    }
                }else{
                    $record['campaign_subject'] = "未知";
                    $record['subscriber_email'] = $record['comment'];
                }
            }
            return [
                'total'=>$totalCount,
                'rows'=>$records
            ];
    }

    /**已处理
     * @param array $search
     * @param int $page
     * @param int $rows
     * @param string $sort
     * @param string $order
     * @return array
     * @throws \think\Exception
     */
    public function loadUnidentifiedBounces($search=array(),
                                         $page=1,
                                         $rows=DEFAULT_PAGE_ROWS,
                                         $sort = '',
                                         $order = ''){
        /////////////////////////////////////////////////
        $limit = ($page - 1) * $rows . "," . $rows;
        $order = 'bounce_id desc';
        /////////////////////////////////////////////////
        $conditions = [];
        $conditions['processed_status'] = IndexDefs::eBounceUnidentified;

        if(!emptyInArray($search, 'mailbox')){
            $conditions['mailbox_id'] = $search['mailbox'];
        }
        $totalCount = Db::table('bounces')->where($conditions)->count();
        $records = Db::table('bounces')->where($conditions)
            ->limit($limit)
            ->order($order)
            ->field('bounce_id, date, processed_status')
            ->select();
        foreach($records as &$record){
            $record['campaign_subject'] = "未知";
            $record['subscriber_email'] = "未知";
        }
        return [
            'total'=>$totalCount,
            'rows'=>$records
        ];

    }
    public function loadBounceIds($bounceProcessedStatus, $search){
        $conditions = [];
        if($bounceProcessedStatus == IndexDefs::eBounceUnidentified){
            //未识别
            $conditions['processed_status'] = IndexDefs::eBounceUnidentified;
        }else{
            //已处理
            $conditions['processed_status'] = ['<>', IndexDefs::eBounceUnidentified];
        }
        if(!emptyInArray($search, 'mailbox')){
            $conditions['mailbox_id'] = $search['mailbox'];
        }
        $bounceIds = Db::table('bounces')->where($conditions)->column('bounce_id');
        return $bounceIds;
    }
    public function getBounce($bounceId){
        $bounce = Db::table('bounces')->where('bounce_id', $bounceId)->field('*')->find();
        return $bounce;
    }
    public function deleteBounce($bounceId){
        Db::table('bounces')->where('bounce_id', $bounceId)->delete();
        Db::table('bounces_campaign_subscriber')->where('bounce_id', $bounceId)->delete();
        return true;
    }
    public function processBounceAction($bounceIds, $action){
        foreach($bounceIds as $bounceId){
            $subscriberId = 0;
            $campaignId = 0;
            $record = Db::table('bounces_campaign_subscriber')
                ->where('bounce_id', $bounceId)
                ->field('subscriber_id, campaign_id')
                ->find();
            if($record){
                $subscriberId = $record['subscriber_id'];
                $campaignId = $record['campaign_id'];
            }
            $subscribersLogic = SubscribersLogic::newObj();
            switch($action){
                case IndexDefs::eBounceActionDeleteSubscriber:
                    $subscribersLogic->deleteSubscriber($subscriberId);
                    break;
                case IndexDefs::eBounceActionDeleteSubscriberAndBounce:
                    $subscribersLogic->deleteSubscriber($subscriberId);
                    $this->deleteBounce($bounceId);
                    break;
                case IndexDefs::eBounceActionBlacklistSubscriber:
                    $subscribersLogic->blacklistSubscriber($subscriberId);
                    break;
                case IndexDefs::eBounceActionBlacklistSubscriberAndDeleteBounce:
                    $subscribersLogic->blacklistSubscriber($subscriberId);
                    $this->deleteBounce($bounceId);
                    break;
                case IndexDefs::eBounceActionDeleteBounce:
                    $this->deleteBounce($bounceId);
                    break;
                default:{

                }
            }
        }
        return true;
    }
}