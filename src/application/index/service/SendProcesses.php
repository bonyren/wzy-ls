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
use think\Db;
use app\index\logic\Defs;
class SendProcesses{
	protected static $_instance = null;
	public static function I(){
		if(self::$_instance == null){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}
?>