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
namespace app\cli\controller;
use think\Controller;
use think\Db;
use think\Log;
use think\Request;
use app\cli\Jobs\ProcessQueue;
use app\cli\Jobs\ProcessBounces;
use app\index\mail\SystemEmail;
use app\index\mail\CampaignEmail;

class Tools extends Common
{
    public function _initialize(){
        parent::_initialize();
    }
    //投递邮件
    public function processQueue(){
        $processQueue = new ProcessQueue();
        $processQueue->process();
    }
    //接收退信
    public function processBounces(){
        $processBounces = new ProcessBounces();
        $processBounces->process();
    }
}