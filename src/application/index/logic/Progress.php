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
use think\Db;
use app\index\Defs as IndexDefs;
use think\Debug;
use think\Log;
use think\Session;
use think\Cache;

class Progress extends Base{
    const PROGRESS_SUBSCRIBERS_IMPORT_FILE_TYPE = 1;

    public function register($type){
        Session::set('progress_' . $type, '');
    }
    public function unregister($type){
        Session::delete('progress_' . $type);
    }
    public function updateProgress($type, $content){
        Session::set('progress_' . $type, $content);
    }
    /**return null if not exist
     * @param $type
     * @return mixed
     */
    public function queryProgress($type){
        return Session::get('progress_' . $type);
    }
}