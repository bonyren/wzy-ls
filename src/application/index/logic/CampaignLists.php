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
use app\index\Defs as IndexDefs;
use think\Controller;
use think\Request;
use think\Url;
use think\Log;
use think\Debug;
use app\Defs;
use think\Db;

class CampaignLists extends Base{
    public function loadDraftCampaigns(
        $search=array(),
        $page=1,
        $rows=DEFAULT_PAGE_ROWS,
        $sort = '',
        $order = ''){
        /////////////////////////////////////////////////
        if($sort == 'status'){
            $order = 'status ' . $order;
        }else{
            $order = 'campaign_id desc';
        }
        /////////////////////////////////////////////////
        $conditions = [];
        $conditions['status'] = IndexDefs::eCampaignStatusDraft;
        if(!emptyInArray($search, 'subject')){
            $conditions['subject'] = ['like', "%{$search['subject']}%"];
        }

        $totalCount = Db::table('campaigns')->where($conditions)->count();
        $records = Db::table('campaigns')->where($conditions)
            ->page($page, $rows)
            ->order($order)
            ->field('campaign_id, subject, entered, status, unix_timestamp(now())-unix_timestamp(entered) as age')
            ->select();
        foreach($records as &$record){
            $age = $record['age'];
            $record['age'] = secs2time($age);
            $record['age_array'] = secs2timeArray($age);
        }
        return [
            'total'=>$totalCount,
            'rows'=>$records
        ];
    }
    public function loadActiveCampaigns(
        $search=array(),
        $page=1,
        $rows=DEFAULT_PAGE_ROWS,
        $sort = '',
        $order = ''){
        /////////////////////////////////////////////////
        if($sort == 'status'){
            $order = 'status ' . $order;
        }else{
            $order = 'campaign_id desc';
        }
        /////////////////////////////////////////////////
        $conditions = [];
        $conditions['status'] = ['in', [IndexDefs::eCampaignStatusSubmitted, IndexDefs::eCampaignStatusSuspended, IndexDefs::eCampaignStatusInprogress]];
        if(!emptyInArray($search, 'subject')){
            $conditions['subject'] = ['like', "%{$search['subject']}%"];
        }

        $totalCount = Db::table('campaigns')->where($conditions)->count();
        $records = Db::table('campaigns')->where($conditions)
                ->page($page, $rows)->order($order)->field('campaign_id,
                    subject,
                    embargo,
                    unix_timestamp(embargo)-unix_timestamp(now()) as before_embargo,
                    requeue_interval,
                    stop_after,
                    entered,
                    status')
                ->select();
        return [
            'total'=>$totalCount,
            'rows'=>$records
        ];
    }
    public function loadSentCampaigns(
                                  $search=array(),
                                  $page=1,
                                  $rows=DEFAULT_PAGE_ROWS,
                                  $sort = '',
                                  $order = ''){
        /////////////////////////////////////////////////
        if($sort == 'status'){
            $order = 'status ' . $order;
        }else{
            $order = 'campaign_id desc';
        }
        /////////////////////////////////////////////////
        $conditions = [];
        $conditions['status'] = IndexDefs::eCampaignStatusSent;
        if(!emptyInArray($search, 'subject')){
            $conditions['subject'] = ['like', "%{$search['subject']}%"];
        }
        $totalCount = Db::table('campaigns')->where($conditions)->count();
        $records = Db::table('campaigns')->where($conditions)
            ->page($page, $rows)->order($order)->field('campaign_id, subject, entered, status')->select();
        return [
            'total'=>$totalCount,
            'rows'=>$records
        ];
    }
    public function getSubscriberInfosUnderCampaign($campaignId){
        $subscriberIds = Db::table('list_subscribers')->alias('LS')
            ->join('subscribers S', 'LS.subscriber_id=S.subscriber_id')
            ->join('campaigns_lists CL', 'LS.list_id=CL.list_id')
            ->where([
                'CL.campaign_id'=>$campaignId,
                'S.blacklisted'=>0,
                'S.deleted'=>0
            ])->distinct(true)->column('S.subscriber_id');

        $subscriberHtmlIds = Db::table('list_subscribers')->alias('LS')
            ->join('subscribers S', 'LS.subscriber_id=S.subscriber_id')
            ->join('campaigns_lists CL', 'LS.list_id=CL.list_id')
            ->where([
                'CL.campaign_id'=>$campaignId,
                'S.html_email'=>IndexDefs::eSubscriberHtmlEmail,
                'S.blacklisted'=>0,
                'S.deleted'=>0
            ])->distinct(true)->column('S.subscriber_id');
        return [
            'total_count'=>count($subscriberIds),
            'html_count'=>count($subscriberHtmlIds),
            'text_count'=>count($subscriberIds) - count($subscriberHtmlIds)
        ];
    }

    public function suspendCampaign($campaignId){
        Db::table('campaigns')->where(['campaign_id'=>$campaignId])->where(function($query){
            $query->where('status', IndexDefs::eCampaignStatusSubmitted)->whereOr('status', IndexDefs::eCampaignStatusInprogress);
        })->update(['status'=>IndexDefs::eCampaignStatusSuspended]);
        return true;
    }
    public function requeueCampaign($campaignId){
        Db::table('campaigns')->where(['campaign_id'=>$campaignId])->update([
            'status'=>IndexDefs::eCampaignStatusSubmitted,
            'send_start_time'=>Defs::DEFAULT_DB_DATETIME_VALUE,
            'send_end_time'=>Defs::DEFAULT_DB_DATETIME_VALUE
        ]);
        return true;
    }
    public function markSentCampaign($campaignId){
        Db::table('campaigns')->where(['campaign_id'=>$campaignId, 'status'=>IndexDefs::eCampaignStatusSuspended])->update([
            'status'=>IndexDefs::eCampaignStatusSent,
            'requeue_interval'=>0//must update it to 0, otherwise it will be invoke again.
        ]);
        return true;
    }
}
