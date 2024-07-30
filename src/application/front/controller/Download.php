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
use think\Debug;
use think\Request;
use think\Db;

class Download extends Controller{
    public function downloadCampaignAttach($campaignAttachId){
        $attach = Db::table('campaign_attachments')->where('campaign_attach_id', $campaignAttachId)->field('campaign_attach_id,original_name,save_name,mime_type')->find();
        if(!$attach){
            return $this->fetch('common/error');
        }
        $originalPath = convertUploadSaveName2DiskFullPath($attach['save_name']);
        if(!file_exists($originalPath)){
            return $this->fetch('common/error');
        }
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/force-download');
        header('Content-Length: ' . filesize($originalPath));
        header("Content-Disposition: attachment; filename=" . $attach['original_name']);
        readfile($originalPath);
        exit();
    }
}