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
use think\Log;

class Menu extends Base
{
    //获取单个菜单
    public function getRow($id){
        if (empty($id)) {
            return;
        }
        $row = Db::table('menu')->where('id',$id)->find();
        return $row;
    }
    public function getDefaultRow(){
        return [
            'name'=>'',
            'level'=>1,
            'pid'=>0,
            'c'=>'',
            'a'=>'',
            'params'=>'',
            'icon_cls'=>'',
            'order_id'=>0
        ];
    }
    //添加/编辑菜单
    public function save($id,$data)
    {
        $data['pid'] = intval($data['pid']);
        if (empty($id)) {
            $data['name'] = trim($data['name']);
            if (empty($data['name'])) {
                exception('名称不能为空');
            }
            $id = Db::table('menu')->insert($data,false,true);
        } else {
            if (isset($data['name']) && empty($data['name'])) {
                exception('名称不能为空');
            }
            Db::table('menu')->where('id',$id)->update($data);
        }
        return $id;
    }

    //删除菜单
    public function delete($id){
        $child = Db::table('menu')->where('pid',$id)->limit(1)->find();
        if (!empty($child)) {
            exception('该菜单存在子菜单，无法直接删除');
        }
        $result = Db::table('menu')->where('id',$id)->delete();
        Db::table('admin_role_menu')->where('menu_id', $id)->delete();
        return $result;
    }

}