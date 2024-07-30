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
use app\index\model\AdminRole as AdminRoleModel;
use think\Log;
use think\Db;

class AdminRole extends Base{
    public function load($search=array(),
                         $page=1,
                         $rows=DEFAULT_PAGE_ROWS,
                         $sort = '',
                         $order = ''){
        //排序
        if($sort == 'role_id'){
            $order = 'role_id ' . $order;
        }else{
            $order = 'role_id asc';
        }
        //搜索
        $conditions = [];
        if(!emptyInArray($search, 'role_name')){
            $conditions['role_name'] = ['like', '%'.$search['role_name'].'%'];
        }
        $totalCount = Db::table('admin_role')->where($conditions)->count();
        $records = Db::table('admin_role')
            ->where($conditions)
            ->page($page, $rows)
            ->order($order)
            ->field(true)
            ->select();
        return [
            'total'=>$totalCount,
            'rows'=>$records
        ];
    }
    public function save($roleId, $infos){
        if(!$roleId){
            $model = new AdminRoleModel();
            $model->data($infos);
            $model->allowField(true)->save();
        }else{
            $model = AdminRoleModel::get($roleId);
            if(!$model){
                exception("无法找到该角色");
            }
            $model->allowField(true)->save($infos);
        }
    }
    public function delete($roleId){
        $model = AdminRoleModel::get($roleId);
        if(!$model){
            exception("无法找到该角色");
        }
        AdminRoleModel::destroy($roleId);
        Db::table('admins')->where('role_id', $roleId)->setField('role_id', 0);
        Db::table('admin_role_menu')->where('role_id', $roleId)->delete();
    }
    public function get($roleId){
        $record = Db::table('admin_role')->where('role_id', $roleId)->field(true)->find();
        if(!$record){
            return null;
        }
        return $record;
    }
    public function getDefault(){
        return [
            'role_name'=>'',
            'description'=>''
        ];
    }
}