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
use app\index\model\Setting;
use app\index\Defs as IndexDefs;
use think\Debug;
use think\Db;
use think\Log;
use think\Request;
use think\Session;

class Statis extends Base{
    const DAY_MAILING = 1;
    public static $statisTasks = [
        self::DAY_MAILING=>['name'=>'DAY_MAILING','interval'=>600/*10 minutes*/]
    ];
    const STATIS_TASK_RESULT_OK = 1;
    const STATIS_TASK_RESULT_FAIL = 2;
    /******************************************************************************************************************/
    public function __construct(){
        parent::__construct();
    }
    public function dayMailing(){
        if(!$this->beforeRunTask(self::DAY_MAILING)){
            return;
        }
        ///////////////////////////////////////////
        $nowDate = date('Y-m-d');
        $succssCount = Db::table('campaign_subscribers')->where("DATE(entered)='$nowDate'")->where(['status'=>IndexDefs::eCampaignSubscriberStatusSentSuccess])->count();
        $failedCount = Db::table('campaign_subscribers')->where("DATE(entered)='$nowDate'")->where(['status'=>['in', [IndexDefs::eCampaignSubscriberStatusUnconfirmed,
            IndexDefs::eCampaignSubscriberStatusInvalidEmail,
            IndexDefs::eCampaignSubscriberStatusSentFail]]])->count();
        $existCount = Db::table('statis_day_mailing')->where('date', $nowDate)->count();
        if($existCount){
            Db::table('statis_day_mailing')->where('date', $nowDate)->update([
                'success_count'=>$succssCount,
                'failed_count'=>$failedCount
            ]);
        }else{
            Db::table('statis_day_mailing')->insert([
                'date'=>$nowDate,
                'success_count'=>$succssCount,
                'failed_count'=>$failedCount
            ]);
        }
        //////////////////////////////////////////
        $this->afterRunTask(self::DAY_MAILING, self::STATIS_TASK_RESULT_OK);
    }
    public function loadDayMailing($date){
        $return = [
            'success_count'=>0,
            'failed_count'=>0
        ];
        $rowInfos = Db::table('statis_day_mailing')->where('date', $date)->field('success_count, failed_count')->find();
        if($rowInfos){
            $return['success_count'] = $rowInfos['success_count'];
            $return['failed_count'] = $rowInfos['failed_count'];
        }
        return $return;
    }
    /*******************************************************************************************************************/
    protected function beforeRunTask($identifier){
        $lastRecord = Db::table('statis_tasks')->where('name', self::$statisTasks[$identifier]['name'])->field('name, last_time')->find();
        if($lastRecord){
            $lastTimeStamp = strtotime($lastRecord['last_time']);
            $nowTimeStamp = time();
            if($nowTimeStamp > $lastTimeStamp && ($nowTimeStamp - $lastTimeStamp) < self::$statisTasks[$identifier]['interval']){
                Log::notice("beforeRunTask: not reach the interval condition, skip this trigger");
                return FALSE;
            }
        }
        return TRUE;
    }
    protected function afterRunTask($identifier, $result){
        $existTaskCount = Db::table('statis_tasks')->where('name', self::$statisTasks[$identifier]['name'])->count();
        if($existTaskCount){
            Db::table('statis_tasks')->where('name', self::$statisTasks[$identifier]['name'])->update([
                'last_time'=>date('Y-m-d H:i:s'),
                'last_result'=>$result
            ]);
        }else{
            Db::table('statis_tasks')->insert([
                'name'=>self::$statisTasks[$identifier]['name'],
                'last_time'=>date('Y-m-d H:i:s'),
                'last_result'=>$result
            ]);
        }
    }
}