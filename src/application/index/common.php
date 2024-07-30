<?php
use app\Defs;

function secs2time($secs){
    $years = $days = $hours = $mins = 0;
    $hours = (int) ($secs / 3600);
    $secs = $secs - ($hours * 3600);
    if ($hours > 24) {
        $days = (int) ($hours / 24);
        $hours = $hours - (24 * $days);
    }
    if ($days > 365) { //# a well, an estimate
        $years = (int) ($days / 365);
        $days = $days - ($years * 365);
    }
    $mins = (int) ($secs / 60);
    $secs = (int) ($secs % 60);
    $res = '';
    if ($years) {
        $res .= $years.' 年';
    }
    if ($days) {
        $res .= ' '.$days.' 天';
    }
    if ($hours) {
        $res .= ' '.$hours.' 小时';
    }
    if ($mins) {
        $res .= ' '.$mins.' 分钟';
    }
    if ($secs) {
        $res .= ' '.sprintf('%02d', $secs).' 秒';
    }
    if(empty($res)){
        $res = '0 seconds';
    }
    return $res;
}
function secs2timeArray($secs){
    $years = $days = $hours = $mins = 0;
    $hours = (int) ($secs / 3600);
    $secs = $secs - ($hours * 3600);
    if ($hours > 24) {
        $days = (int) ($hours / 24);
        $hours = $hours - (24 * $days);
    }
    if ($days > 365) { //# a well, an estimate
        $years = (int) ($days / 365);
        $days = $days - ($years * 365);
    }
    $mins = (int) ($secs / 60);
    $secs = (int) ($secs % 60);
    $res = [
        'years'=>$years,
        'days'=>$days,
        'hours'=>$hours,
        'minutes'=>$mins,
        'seconds'=>sprintf('%02d', $secs)
    ];
    return $res;
}
function timeDiff($time1, $time2){
    if (!$time1 || !$time2 || $time1 == Defs::DEFAULT_DB_DATETIME_VALUE || $time2 == Defs::DEFAULT_DB_DATETIME_VALUE) {
        return '未知';
    }
    $t1 = strtotime($time1);
    $t2 = strtotime($time2);

    if ($t1 < $t2) {
        $diff = $t2 - $t1;
    } else {
        $diff = $t1 - $t2;
    }
    if ($diff == 0) {
        return 'little time';
    }
    return secs2time($diff);
}
/**********************************************************************************************************************/