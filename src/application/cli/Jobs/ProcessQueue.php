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
namespace app\cli\Jobs;
use think\Log;
use app\index\logic\ProcessQueue as ProcessQueueLogic;
class ProcessQueue{
    public function __construct(){
    }
    public function process(){
        ProcessQueueLogic::I()->process();
    }
}