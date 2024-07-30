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
use think\Debug;
use think\Db;
use think\Log;

class Init extends Base{
    public function __construct(){
        parent::__construct();
    }
    const XORMASK = 'XORMASK';
    const HMACKEY = 'HMACKEY';

    public function init(){
        $xormask = md5(uniqid(rand(), true));
        $xormask = str_repeat($xormask, 20);
        $this->saveConfig(self::XORMASK, $xormask);
        $hmackey = bin2hex(random_bytes(256));
        $this->saveConfig(self::HMACKEY, $hmackey);
    }
    public function getSetting($key){
        $value = Db::table('init_setting')->where('key', $key)->value('value');
        return $value;
    }
    protected function saveConfig($key, $value){
        $count = Db::table('init_setting')->where('key', $key)->count();
        if($count > 0){
            Db::table('init_setting')->where('key', $key)->update(['value'=>$value]);
        }else{
            Db::table('init_setting')->insert([
                'key'=>$key,
                'value'=>$value
            ]);
        }
    }
}