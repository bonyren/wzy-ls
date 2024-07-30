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
use think\Session;
use think\Db;
use think\Request;
use app\Defs;
use app\index\service\EventLogs as EventLogsService;
use app\index\service\Messages as MessagesService;
use app\index\model\Setting as SettingModel;

/**递归删除文件和目录
 * @param $dir
 */
function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir. DS .$object) && !is_link($dir."/".$object))
                    rrmdir($dir. DS .$object);
                else
                    unlink($dir. DS .$object);
            }
        }
        rmdir($dir);
    }
}

function dict($key = '', $fileName = 'Setting') {
    static $_dictFileCache  =   array();
    $file = APP_PATH . 'dict' . DS  . $fileName . '.php';
    if (!file_exists($file)){
        unset($_dictFileCache);
        return null;
    }
    if(!$key && !empty($_dictFileCache)) return $_dictFileCache;
    if ($key && isset($_dictFileCache[$key])) return $_dictFileCache[$key];
    $data = require_once $file;
    $_dictFileCache = $data;
    return $key ? $data[$key] : $data;
}

/**获取系统设置
 * @param $field
 * @return mixed|null
 */
function systemSetting($field){
    $settingModel = new SettingModel();
    $value = $settingModel->getSetting($field);
    return $value;
}
function initSetting($key){
    $initLogic = new \app\index\logic\Init();
    return $initLogic->getSetting($key);
}
/**记录事件
 * @param $content
 * @param int $severity
 */
function logEvent($content, $severity=EventLogsService::eSeverityInfo){
    EventLogsService::I()->logEvent($content, $severity);
}

/**管理員消息
 * @param $title
 * @param $content
 */
function adminMessage($title, $content){
    MessagesService::I()->sendAdminMessage($title, $content);
}
/*************************************************************************/
function convertUploadSaveName2FullUrl($saveName){
    $urlFilePath = str_replace(DS, '/' , $saveName);
    $url = UPLOAD_URL_ROOT . $urlFilePath;
    return $url;
}
function convertUploadSaveName2RelativeUrl($saveName){
    $urlFilePath = str_replace(DS, '/' , $saveName);
    $url = UPLOAD_FOLDER . '/' . $urlFilePath;
    return $url;
}
function convertUploadSaveName2AbsoluteUrl($saveName){
    $urlFilePath = str_replace(DS, '/' , $saveName);
    $url = SCRIPT_DIR . '/' . UPLOAD_FOLDER . '/' . $urlFilePath;
    return $url;
}
function convertUploadSaveName2DiskFullPath($saveName){
    $diskPath = UPLOAD_DIR . DS . $saveName;
    return $diskPath;
}
function convertUploadRelativeUrl2DiskFullPath($relativeUrl){
    $localRelativePath = str_replace('/', DS , $relativeUrl);
    return SITE_DIR . DIRECTORY_SEPARATOR . $localRelativePath;
}
function convertUploadSaveNameThumbnail2DiskFullPath($saveName){
    $thumbnailPath = UPLOAD_DIR . DS . 'thumbnails' . DS . basename($saveName);
    return $thumbnailPath;
}

/**
 * 生成上传文件的完整url
 * @param $url
 */
function generateUploadFullUrl($url){
    if(startsWith($url, SCHEMA)){
        return $url;
    }
    if(startsWith($url, '/')){
        return SITE_URL . $url;
    }
    return SITE_URL . '/' . $url;
}
/*************************************************************************/
function getUnzipFullPath(){
    return BIN_DIR . DIRECTORY_SEPARATOR . 'unzip.exe';
}
function getMysqldumpFullPath(){
    return BIN_DIR . DIRECTORY_SEPARATOR . 'mysqldump.exe';
}
function emptyInArray(&$arr, $key){
    if(!isset($arr[$key])){
        return true;
    }
    return empty($arr[$key]);
}
function emptyStringInArray(&$arr, $key){
    if(!isset($arr[$key])){
        return true;
    }
    return $arr[$key] === '';
}
function generateUniqid(){
    if(version_compare(PHP_VERSION, '7.0.0') >= 0) {
        $id = bin2hex(random_bytes(16));
    }else{
        $id = md5(uniqid());
    }
    return $id;
}
/**********************************************************************************************************************/
function createFormToken(){
    $token = generateUniqid();
    Session::set('form-token', $token);
    return $token;
}
function verifyFormToken($formToken){
    $token = Session::get('form-token');
    if($token && $token == $formToken){
        Session::set('form-token', '');
        return true;
    }else{
        return false;
    }
}
/****************************************************************************************/
function password($password, $encrypt='') {
    $pwd = array();
    $pwd['encrypt'] =  $encrypt ? $encrypt : \think\helper\Str::random(6);
    $pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
    return $encrypt ? $pwd['password'] : $pwd;
}

/**日期过滤
 * @param $input
 * @return string
 */
function dateFilter($input){
    if($input == Defs::DEFAULT_DB_DATE_VALUE){
        return '';
    }
    return $input;
}
function dateTimeFilter($input){
    if($input == Defs::DEFAULT_DB_DATETIME_VALUE){
        return '';
    }
    return $input;
}
function dateDbConverter($input){
    if(empty($input)){
        return Defs::DEFAULT_DB_DATE_VALUE;
    }
    return $input;
}
function dateTimeDbConverter($input){
    if(empty($input)){
        return Defs::DEFAULT_DB_DATETIME_VALUE;
    }
    return $input;
}
/****************************************************************************************/
function tableExists($table) {
    $sql = "SHOW TABLES LIKE '" . $table . "'";
    $info = Db::query($sql);
    if (!empty($info)) {
        return true;
    } else {
        return false;
    }
}
function isWeixinVisit(){
    $userAgent = Request::instance()->header('user-agent');
    if (stripos($userAgent, 'MicroMessenger') !== false) {
        return true;
    } else {
        return false;
    }
}
function getWeekdayText($weekDay){
    $weekDays = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
    if(isset($weekDays[$weekDay])){
        return $weekDays[$weekDay];
    }else{
        return '';
    }
}
function convertLineBreakToEscapeChars($str){
    return str_replace("'", "\'", str_replace("\n", "\\n", str_replace("\r\n", "\\r\\n", $str)));
}