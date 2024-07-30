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

namespace app\front\controller;

use think\Controller;
use think\Log;
use think\Db;
use think\Debug;
use think\Request;
use app\Defs;
use app\front\logic\Track as TrackLogic;
use app\index\logic\Subscribers as SubscribersLogic;
class Track extends Controller
{
    public function image($uid=0, $cid=0){
        if (!empty($uid) && !empty($cid)) {
            $uid = preg_replace('/\W/', '', $uid);
            $subscribersLogic = SubscribersLogic::I();
            $subscriberData = $subscribersLogic->getSubscriberByUniqid($uid);
            if($subscriberData){
                $subscriberId = $subscriberData['subscriber_id'];
                $campaignId = $cid;
                Db::table('campaign_subscribers')->where(['campaign_id'=>$campaignId,
                    'subscriber_id'=>$subscriberId,
                    'viewed'=>['=', Defs::DEFAULT_DB_DATETIME_VALUE]])->update(['viewed'=>date('Y-m-d H:i:s')]);
                Db::table('campaigns')->where('campaign_id', $campaignId)->setInc('viewed_count');
            }else{
                Log::error("can't find it, uid: $uid, cid: $cid");
            }
        }else{
            Log::error("invalid request");
        }
        /***********************************************************************/
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: image/png');
        $imageContent =  base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAABGdBTUEAALGPC/xhBQAAAAZQTFRF////AAAAVcLTfgAAAAF0Uk5TAEDm2GYAAAABYktHRACIBR1IAAAACXBIWXMAAAsSAAALEgHS3X78AAAAB3RJTUUH0gQCEx05cqKA8gAAAApJREFUeJxjYAAAAAIAAUivpHEAAAAASUVORK5CYII=');
        header('Content-Length: ' . strlen($imageContent));
        echo $imageContent;
        exit();
    }
    public function tracking($trackingId){
        $trackLogic = new TrackLogic();
        $url = $trackLogic->convertToOriginalUrl($trackingId);
        if($url  === false){
            return $this->fetch('common/error');
        }else{
            header('Location: '.$url, true, 303);
            exit();
        }
    }
}