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
namespace app\front\logic;
use think\Debug;
use think\Db;
use think\Log;
use app\Defs;
use app\index\logic\Init as InitLogic;

class Track extends Base{
    public function __construct(){
        parent::__construct();
    }
    public function convertToOriginalUrl($trackingId){
        if(empty($trackingId)){
            return false;
        }
        $track = base64_decode($trackingId);
        $XORMask = initSetting(InitLogic::XORMASK);
        $track = $track ^ $XORMask;

        if(!preg_match('/^(H|T)\|([1-9]\d*)\|([1-9]\d*)\|([1-9]\d*)$/', $track, $matches)) {
            return false;
        }
        $campaignType = $matches[1];
        $linkTrackId = $matches[2];
        $campaignId = $matches[3];
        $subscriberId = $matches[4];
        $linkData = Db::table('link_tracks')->where(['link_track_id'=>$linkTrackId])->field('link_track_id, url, uuid, personalise')->find();
        if($linkData === null){
            return false;
        }
        /////////////////////////////////////////////
        $allowed = Db::table('campaign_subscribers')->where(['campaign_id'=>$campaignId, 'subscriber_id'=>$subscriberId])->count();
        if(!$allowed){
            return false;
        }
        ///////////////////////////////////////////////////////////////////////////////////////////////////////
        $firstClick = Db::table('link_track_campaign')->where(['campaign_id'=>$campaignId, 'link_track_id'=>$linkTrackId])->value('first_click');
        if($firstClick === null){
            return false;
        }
        if($firstClick == Defs::DEFAULT_DB_DATETIME_VALUE){
            Db::table('link_track_campaign')->where(['campaign_id'=>$campaignId, 'link_track_id'=>$linkTrackId])->update([
                'first_click'=>Db::raw('now()'),
                'latest_click'=>Db::raw('now()'),
                'clicked'=>Db::raw('clicked+1')
            ]);
        }else{
            Db::table('link_track_campaign')->where(['campaign_id'=>$campaignId, 'link_track_id'=>$linkTrackId])->update([
                'latest_click'=>Db::raw('now()'),
                'clicked'=>Db::raw('clicked+1')
            ]);
        }
        if($campaignType == 'H'){
            Db::table('link_track_campaign')->where(['campaign_id'=>$campaignId, 'link_track_id'=>$linkTrackId])->setInc('html_clicked');
        }else{
            Db::table('link_track_campaign')->where(['campaign_id'=>$campaignId, 'link_track_id'=>$linkTrackId])->setInc('text_clicked');
        }
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        $viewed = Db::table('campaign_subscribers')->where(['campaign_id'=>$campaignId, 'subscriber_id'=>$subscriberId])->value('viewed');
        if($viewed !== null){
            Db::table('campaign_subscribers')->where(['campaign_id'=>$campaignId, 'subscriber_id'=>$subscriberId])->update([
                'viewed'=>date('Y-m-d H:i:s')
            ]);
            Db::table('campaigns')->where('campaign_id', $campaignId)->setInc('viewed_count');
        }
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        $firstClick = Db::table('link_track_subscriber')->where(['campaign_id'=>$campaignId, 'subscriber_id'=>$subscriberId, 'link_track_id'=>$linkTrackId])
            ->value('first_click');
        if($firstClick === null){
            //insert at this time
            Db::table('link_track_subscriber')->insert([
                'campaign_id'=>$campaignId,
                'subscriber_id'=>$subscriberId,
                'link_track_id'=>$linkTrackId,
                'first_click'=>Db::raw('now()'),
                'latest_click'=>Db::raw('now()'),
                'clicked'=>1
            ]);
        }else {
            Db::table('link_track_subscriber')->where(['campaign_id' => $campaignId, 'subscriber_id' => $subscriberId, 'link_track_id' => $linkTrackId])->update([
                'latest_click' => Db::raw('now()'),
                'clicked' => Db::raw('clicked+1')
            ]);
        }
        if($campaignType == 'H'){
            Db::table('link_track_subscriber')->where(['campaign_id'=>$campaignId, 'subscriber_id'=>$subscriberId, 'link_track_id'=>$linkTrackId])->setInc('html_clicked');
        }else{
            Db::table('link_track_subscriber')->where(['campaign_id'=>$campaignId, 'subscriber_id'=>$subscriberId, 'link_track_id'=>$linkTrackId])->setInc('text_clicked');
        }
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        $url = $linkData['url'];
        return $url;
    }
}