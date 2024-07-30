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
use app\index\model\Base as BaseModel;

class LoginLogs extends Base
{
    protected function __construct(){
        parent::__construct();
    }
    public function load($search=array(),
                         $page=1,
                         $rows=DEFAULT_PAGE_ROWS,
                         $sort = '',
                         $order = ''){
        if($sort == 'time') {
            $order = 'time ' . $order;
        }else {
            $order = 'id desc';
        }
        $conditions = [];
        if(!emptyInArray($search, 'username')){
            $conditions['username'] = ['like', '%'.$search['username'].'%'];
        }
        $totalCount = Db::table('login_logs')->where($conditions)->count();
        $records = Db::table('login_logs')
            ->where($conditions)
            ->order($order)
            ->page($page, $rows)
            ->field(true)
            ->select();
        return [
            'total'=>$totalCount,
            'rows'=>$records
        ];
    }
    public function add($username, $userid, $usertype, $useragent, $ip){
        Db::table('login_logs')->insert([
            'username'=>$username,
            'userid'=>$userid,
            'usertype'=>$usertype,
            'useragent'=>$useragent,
            'device'=>Request::instance()->isMobile()?BaseModel::AUDIT_LOG_MOBILE_DEVICE:BaseModel::AUDIT_LOG_DESKTOP_DEVICE,
            'ip'=>$ip,
            'time'=>Db::raw('now()')
        ]);
    }
}