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
use think\Request;

class AppConf{
    protected function __construct(){
        $this->load();
    }
    public static function I(){
        static $instance = null;
        if(!$instance){
            $instance = new self();
        }
        return $instance;
    }
    const DEFAULT_SCHEMA = 'http';
    const DEFAULT_DOMAIN = '127.0.0.1';
    const DEFAULT_PORT = '1080';
    protected $schema = self::DEFAULT_SCHEMA;
    protected $domain = self::DEFAULT_DOMAIN;
    protected $port = self::DEFAULT_PORT;

    /**获取绑定的web address
     * @return string
     */
    public function siteUrl(){
        if(Request::instance()->isCli()){
            if($this->port == 80){
                return $this->schema . '://' . $this->domain;
            }else {
                return $this->schema . '://' . $this->domain . ":" . $this->port;
            }
        }else{
            return SITE_URL;
        }
    }
    public function siteDomain(){
        if(Request::instance()->isCli()){
            return $this->domain();
        }else{
            return request()->host();
        }
    }
    /******************************************************************************************************************/
    protected function load(){
        $this->schema = $this->schema();
        $this->domain = $this->domain();
        $this->port = $this->port();
    }
    protected function schema(){
        $schema = $this->getConfValue('app', 'schema');
        if(!$schema){
            $schema = self::DEFAULT_SCHEMA;
        }
        return $schema;
    }
    protected function domain(){
        $domain = $this->getConfValue('app', 'domain');
        if(!$domain){
            $domain = self::DEFAULT_DOMAIN;
        }
        return $domain;
    }
    protected function port(){
        $port = $this->getConfValue('app', 'port');
        if(!$port){
            $port = self::DEFAULT_PORT;
        }
        return $port;
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    protected function getConfValue($section, $name){
        $path = $this->getConfFilePath();
        if(!file_exists($path)){
            return false;
        }
        $result = parse_ini_file($this->getConfFilePath(), true);
        if(!$result){
            return false;
        }
        if(!is_array($result)){
            return false;
        }
        if(!isset($result[$section]) || !is_array($result[$section])){
            return false;
        }
        if(!isset($result[$section][$name])){
            return false;
        }
        return $result[$section][$name];
    }
    protected function getConfFilePath(){
        $path = ROOT_PATH . ".." . DS . "conf" . DS . "app.ini";
        return $path;
    }
}