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
use think\Debug;
use think\Db;
use think\Log;
use think\Request;
use think\Session;
use app\index\Defs as IndexDefs;

class Config extends Base{
    protected function __construct(){
        parent::__construct();
    }
    /************************评估分类*****************************************************************************/
    public function loadCategories(){
        $records = Db::table('subject_category')->field('id,name')->order('id asc')->select();
        return $records;
    }
    public function getCategoryInfos($id){
        $infos = Db::table('subject_category')->where('id', $id)->field('id,name')->find();
        return $infos;
    }
    public function saveCategory($id, $infos){
        if($id){
            //update
            Db::table('subject_category')->where('id', $id)->update($infos);
        }else{
            //insert
            Db::table('subject_category')->insert($infos);
        }
        return true;
    }
    public function deleteCategory($id){
        Db::table('subject_category')->where('id', $id)->delete();
        return true;
    }
}