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
namespace app\index\service;
use think\Debug;
use think\Log;
use think\Db;

class Statistic{
    protected function __construct(){

    }
    public static function I(){
        static $instance = null;
        if(!$instance){
            $instance = new self();
        }
        return $instance;
    }
}