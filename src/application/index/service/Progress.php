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
use think\Session;
use think\Log;
use think\Debug;

class Progress{
    protected function __construct(){

    }
    public static function I(){
        static $instance = null;
        if(!$instance){
            $instance = new self();
        }
        return $instance;
    }

    const PROGRESS_SUBSCRIBERS_IMPORT_FILE_TYPE = 1;
    public static $progressTypeDefs = [
      self::PROGRESS_SUBSCRIBERS_IMPORT_FILE_TYPE=>'PROGRESS_SUBSCRIBERS_IMPORT_FILE_TYPE'
    ];
    public function register($type){
        Session::set(self::$progressTypeDefs[$type] . '.' . 'start_time', date('Y-m-d H:i:s'));
        Session::set(self::$progressTypeDefs[$type] . '.' . 'current_progress', '');
    }
    public function unregister($type){
        Session::delete(self::$progressTypeDefs[$type] . '.' . 'start_time');
        Session::delete(self::$progressTypeDefs[$type] . '.' . 'current_progress');
    }
    public function updateProgress($type, $currentProgress){
        if(!Session::has(self::$progressTypeDefs[$type] . '.' . 'start_time')){
            return;
        }
        Session::set(self::$progressTypeDefs[$type] . '.' . 'current_progress', $currentProgress);
    }
    public function queryProgress($type){
        if(!Session::has(self::$progressTypeDefs[$type] . '.' . 'start_time')){
            return '';
        }
        return Session::get(self::$progressTypeDefs[$type] . '.' . 'current_progress');
    }
}