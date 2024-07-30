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
use app\cli\Jobs\BackupDB;
use app\cli\Jobs\CleanServer;
use app\cli\Jobs\ProcessQueue;
use app\cli\Jobs\ProcessBounces;

use think\Log;

/**
 * 各种任务入口在此定义
 * @package app\scheduler\controller
 */
class Jobs
{
    //数据库备份
    public function backupDB() {
        Log::notice('Jobs::backupDB');
        try {
            $backup = new BackupDB();
            $backup->cleanUp();
            $backup->backup();
            //return true;
            return 1;
        } catch (\Exception $e) {
            //return false;
            return 0;
        }
    }
    //清理服务器，包括数据库过期数据，系统日志文件，应用日志文件
    public function cleanServer(){
        Log::notice('Jobs::cleanServer');
        try {
            $clean = new CleanServer();
            $clean->clean();
            //return true;
            return 1;
        } catch (\Exception $e) {
            //return false;
            return 0;
        }
    }
    //投递邮件
    /**
     * @return bool true:成功， false:失败
     */
    public function processQueue(){
        Log::notice('Jobs::processQueue');
        $processQueue = new ProcessQueue();
        $processQueue->process();
        Log::notice('Jobs::processQueue end');
        //return true;
        return 1;//to avoid the error in cli scene (php index.php /cli/Jobs/processBounces)
    }
    //接收退信
    public function processBounces(){
        Log::notice('Jobs::processBounces');
        $processBounces = new ProcessBounces();
        $processBounces->process();
        //return true;
        return 1;
    }
}