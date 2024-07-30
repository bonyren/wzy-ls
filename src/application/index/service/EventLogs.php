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
use think\Log;
use think\Db;
use think\Debug;
/*
 * CREATE TABLE `event_logs` (
  `event_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `severity` tinyint(4) NOT NULL DEFAULT '3' COMMENT '1-error,2-warning,3-info',
  `entered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `entry` text NOT NULL,
  PRIMARY KEY (`event_log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=180 DEFAULT CHARSET=utf8;
 */
class EventLogs{
    const eSeverityError = 1;
    const eSeverityWarning = 2;
    const eSeverityInfo = 3;
    public static $eSeverityDefs = array(
        self::eSeverityError=>'error',
        self::eSeverityWarning=>'warning',
        self::eSeverityInfo=>'info'
    );
    protected function __construct(){}
    public static function I(){
        static $instance = null;
        if(!$instance){
            $instance = new self();
        }
        return $instance;
    }
    public function logEvent($entry, $severity=self::eSeverityInfo){
        $result = Db::table('event_logs')->insert([
            'entered'=>date('Y-m-d H:i:s'),
            'entry'=>$entry,
            'severity'=>$severity,
            'user_id'=>RequestContext::I()->loginUserId
        ]);
        return $result == 1?true:false;
    }
}