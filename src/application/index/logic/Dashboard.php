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
use think\Debug;
use think\Db;
use think\Log;
use think\Session;
use app\index\Defs as IndexDefs;
class Dashboard extends Base{

    protected function __construct(){
        parent::__construct();
    }
    public function loadStatistic(){
        $statistic = [
            'totalSubscriberCount'=>0,//总订阅者数量
            'totalSubscriberAvailableCount'=>0,//总有效订阅者数量

            'totalCampaignCount'=>0,//总投递活动
            'totalCampaignRunningCount'=>0,//"投递中"活动

            'totalSentCount'=>0,//总投递
            'totalSentRecentCount'=>0,//近一个月投递
            'totalEventCount'=>0
        ];
        $statistic['totalSubscriberCount'] = Db::table('subscribers')->where(['deleted'=>0])->count();
        $statistic['totalSubscriberAvailableCount'] = Db::table('subscribers')->where(['deleted'=>0, 'blacklisted'=>0])->count();

        $statistic['totalCampaignCount'] = Db::table('campaigns')->count();
        $statistic['totalCampaignRunningCount'] = Db::table('campaigns')->where('status', 'in', [IndexDefs::eCampaignStatusSubmitted, IndexDefs::eCampaignStatusInprogress])->count();

        $statistic['totalSentCount'] = Db::table('campaign_subscribers')->where('status', IndexDefs::eCampaignSubscriberStatusSentSuccess)->count();

        $today = date('Y-m-d');
        $beginDate = date('Y-m-d', strtotime($today . ' -1 month'));
        $statistic['totalSentRecentCount'] = Db::table('campaign_subscribers')->where('status', IndexDefs::eCampaignSubscriberStatusSentSuccess)->whereTime('entered', '>=', $beginDate)->count();

        $statistic['totalEventCount'] = Db::table('event_logs')->count();
        return $statistic;
    }
    public function loadEvents($page=1, $rows=DEFAULT_PAGE_ROWS, $sort = '', $order = ''){
        $order = 'event_log_id desc';
        $conditions = [];
        $totalCount = Db::table('event_logs')->where($conditions)->count();
        $records = Db::table('event_logs')
            ->where($conditions)
            ->page($page, $rows)
            ->order($order)
            ->field('*')
            ->select();
        return [
            'total'=>$totalCount,
            'rows'=>$records
        ];
    }
}