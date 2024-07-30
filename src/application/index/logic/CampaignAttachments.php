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

class CampaignAttachments extends Base{
    public function getAttachments($campaignId){
        $attachments = Db::table('campaign_attachments')
            ->where('campaign_id', $campaignId)
            ->field(true)->select();

        for($i=0,$count=count($attachments); $i<$count; $i++){
            $saveName = $attachments[$i]['save_name'];
            $attachments[$i]['relative_url'] = convertUploadSaveName2RelativeUrl($saveName);
            $attachments[$i]['full_url'] = convertUploadSaveName2FullUrl($saveName);
        }
        return $attachments;
    }
}