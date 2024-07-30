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
use app\index\model\Setting;
use think\Debug;
use think\Db;
use think\Log;
use think\Request;
use think\Session;

class Hyperlinks extends Base{
    public function __construct(){
        parent::__construct();
    }
    public function loadCampaignHyperlinks($campaignId){
        $records = Db::table('link_track_campaign')->alias('C')->join('link_tracks L', 'C.link_track_id=L.link_track_id')
            ->where('C.campaign_id', $campaignId)->field('L.url,
                C.campaign_id,
                C.link_track_id,
                C.first_click,
                C.latest_click,
                C.total,
                C.clicked,
                C.html_clicked,
                C.text_clicked')->select();
        return $records;
    }
    public function loadSubscriberHyperlinks($campaignId,
                                             $linkTrackId,
                                             $search=[],
                                             $page=1,
                                             $rows=DEFAULT_PAGE_ROWS,
                                             $sort='',
                                             $order=''){
        $limit = ($page - 1) * $rows . "," . $rows;
        $order = 'subscriber_id desc';
        //条件
        $conditions = ['L.campaign_id'=>$campaignId, 'L.link_track_id'=>$linkTrackId];

        $totalCount = Db::table('link_track_subscriber')->alias('L')->where($conditions)->count();
        $records = Db::table('link_track_subscriber')->alias('L')->join('subscribers S', 'L.subscriber_id=S.subscriber_id')
            ->where($conditions)->limit($limit)->order($order)->field('L.campaign_id,
            L.subscriber_id,
            L.link_track_id,
            L.first_click,
            L.latest_click,
            L.clicked,
            L.html_clicked,
            L.text_clicked,
            S.email')->select();
        return [
            'total'=>$totalCount,
            'rows'=>$records
        ];
    }
}