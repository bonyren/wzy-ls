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
use app\index\model\Setting as SettingModel;
use app\index\service\RequestContext;
use think\Debug;
use think\Db;
use think\Log;
use think\Request;
use think\Session;
use think\Url;
use think\Cache;
use app\index\model\Admins as AdminsModel;
use app\index\Defs as IndexDefs;

class Admins extends Base{
    protected function __construct(){
        parent::__construct();
    }
    public function load($search=array(),
                         $page=1,
                         $rows=DEFAULT_PAGE_ROWS,
                         $sort = '',
                         $order = ''){
        //排序
        if($sort == 'admin_id'){
            $order = 'A.admin_id ' . $order;
        }else{
            $order = 'A.admin_id desc';
        }
        //搜索
        $conditions = [];
        if(!emptyInArray($search, 'realname')){
            $conditions['realname'] = ['like', '%'.$search['realname'].'%'];
        }
        $totalCount = Db::table('admins')->where($conditions)->count();
        $records = Db::table('admins')->alias('A')
            ->join('admin_role R', 'A.role_id=R.role_id', 'LEFT')
            ->where($conditions)
            ->page($page, $rows)
            ->order($order)
            ->field('A.*,R.role_name')
            ->select();
        return [
            'total'=>$totalCount,
            'rows'=>$records
        ];
    }
    protected function password($password, $encrypt='') {
        $pwd = array();
        $pwd['encrypt'] =  $encrypt ? $encrypt : \think\helper\Str::random(6);
        $pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
        return $encrypt ? $pwd['password'] : $pwd;
    }
    public function addAdmin($infos){
        $adminsModel = new AdminsModel();

        $infos['password_changed'] = date('Y-m-d');

        $passwordInfos = $this->password($infos['login_password']);
        $infos['login_password'] = $passwordInfos['password'];
        $infos['login_encrypt'] = $passwordInfos['encrypt'];
        if(isset($infos['super_user'])){
            $infos['role_id'] = 0;
        }
        $adminsModel->allowField(true)->save($infos);
        return true;
    }
    public function editAdmin($adminId, $infos){
        $adminsModel = AdminsModel::get($adminId);
        if(!$adminsModel){
            exception("无法找到该管理员");
        }
        if(isset($infos['super_user'])){
            $infos['role_id'] = 0;
        }
        if(!isset($infos['disabled'])){
            $infos['disabled'] = AdminsModel::eAdminEnableStatus;
        }
        $adminsModel->data($infos);
        $adminsModel->allowField(true)->save();
        return true;
    }
    public function deleteAdmin($adminId){
        $adminsModel = AdminsModel::get($adminId);
        if(!$adminsModel){
            exception("无法找到该管理员");
        }
        $adminsModel->delete();
        return true;
    }
    public function changeAdminPwd($adminId, $infos){
        $adminsModel = AdminsModel::get($adminId);
        if(!$adminsModel){
            exception("无法找到该管理员");
        }
        $newPassword = isset($infos['login_password'])?$infos['login_password']:'';
        if(empty($newPassword)){
            exception("新密码非法");
        }
        $datas = [];
        $passwordInfos = $this->password($newPassword);
        $datas['login_password'] = $passwordInfos['password'];
        $datas['login_encrypt'] = $passwordInfos['encrypt'];
        $adminsModel->allowField(true)->save($datas);
        return true;
    }
    public function getAdminInfos($adminId){
        $adminsModel = AdminsModel::get($adminId);
        return $adminsModel;
    }
    public function getAdminIds(){
        $adminIds = Db::table('admins')->where(['disabled'=>AdminsModel::eAdminEnableStatus])->column('admin_id');
        return $adminIds;
    }
    public function login($username, $password){
        $adminsModel = AdminsModel::get(['login_name'=>$username]);
        if(!$adminsModel){
            exception("无法找到该管理员");
        }
        if($adminsModel->disabled != AdminsModel::eAdminEnableStatus){
            exception("该管理员被禁用");
        }
        $encryptPassword = $this->password($password, $adminsModel->login_encrypt);
        if($encryptPassword != $adminsModel->login_password){
            exception("密码错误");
        }
        Session::set('usertype', IndexDefs::LOGIN_USER_ADMIN_TYPE);
        Session::set('userid', $adminsModel->admin_id);
        Session::set('username', $adminsModel->login_name);
        Session::set('realname', $adminsModel->realname);
        Session::set('userroleid', $adminsModel->role_id);
        Session::set('super_user', $adminsModel->super_user == \app\index\model\Admins::eAdminSuperRole);
        Session::set('lastlogintime', time());
        Session::set('lastloginip', request()->ip());
        Cache::set('SESSION_ID_' . $adminsModel->admin_id . IndexDefs::LOGIN_USER_ADMIN_TYPE, session_id());

        $loginLogsLogic = \app\index\logic\LoginLogs::newObj();
        $loginLogsLogic->add($adminsModel->login_name,
            $adminsModel->admin_id,
            IndexDefs::LOGIN_USER_ADMIN_TYPE,
            truncateString(request()->header('user-agent'), 100),
            request()->ip());
        return $adminsModel;
    }
    public function logout(){
        Session::delete(['usertype',
            'userid',
            'username',
            'realname',
            'userroleid',
            'super_user',
            'lastlogintime',
            'lastloginip']
        );
    }
    public function modifyAdminPwd($adminId, $oldPassword, $newPassword){
        $adminsModel = AdminsModel::get($adminId);
        if(!$adminsModel){
            exception("无法找到该管理员");
        }
        $encryptPassword = $this->password($oldPassword, $adminsModel->login_encrypt);
        if($encryptPassword != $adminsModel->login_password){
            exception("旧密码错误");
        }
        $datas = [];
        $passwordInfos = $this->password($newPassword);
        $datas['login_password'] = $passwordInfos['password'];
        $datas['login_encrypt'] = $passwordInfos['encrypt'];
        $adminsModel->save($datas);
        return true;
    }
    /*********************************************************************/
    public function getAdminRolePairs(){
        return Db::table('admin_role')->column('role_id, role_name');
    }
    protected function checkIfSelectedRecursively($id){
        if(RequestContext::I()->loginSuperUser){
            return true;
        }
        $roleMenu = Db::table('admin_role_menu')->where(array('role_id'=>RequestContext::I()->loginUserRoleId, 'menu_id'=>$id))->find();
        if($roleMenu){
            return true;
        }

        $subIds = Db::table('menu')->where(array('pid'=>$id))->field("id")->select();
        if(empty($subIds)){
            return false;
        }
        foreach ($subIds as $key => $value) {
            $subId = $value['id'];
            $result = $this->checkIfSelectedRecursively($subId);
            if($result){
                return true;
            }
        }
        return false;
    }
    public function loadLeftMenuRecursively($pid, $ptext, &$nodes){
        $rows = Db::table('menu')->where(array('pid'=>$pid))
            ->field("id,pid,level,name,icon_cls,c,a,params,order_id")
            ->order('order_id ASC')->select();
        if(empty($rows)){
            return;
        }
        foreach ($rows as $key => $value) {
            $id = $value['id'];
            if(!$this->checkIfSelectedRecursively($id)){
                continue;
            }
            $node = array();
            $node['id'] = $id;
            $node['name'] = $value['name'];
            $node['text'] = $value['name'];
            $node['c'] = $value['c'];
            $node['a'] = $value['a'];
            $node['params'] = $value['params'];
            $node['order_id'] = $value['order_id'];
            $breadcrumb = empty($ptext)?$value['name']:$ptext . ' &gt; ' . $value['name'];
            $node['attributes'] = [
                'breadcrumb'=>$breadcrumb
            ];
            if(!empty($value['c']) && !empty($value['a'])){
                $node['url'] = url($value['c'] . '/' . $value['a'], $value['params']);
            }
            $node['iconCls'] = $value['icon_cls'];
            $subNodes = array();
            $this->loadLeftMenuRecursively($id, $breadcrumb, $subNodes);
            if(!empty($subNodes)){
                $node['children'] = $subNodes;
            }
            $nodes[] = $node;
        }
        return;
    }

    /**
     * @param $ids
     * @return array
     */
    public function getAdminsByIds($ids) {
        $admins = AdminsModel::where('admin_id','in',$ids)
            ->column('admin_id,login_name,realname,email','admin_id');
        return $admins;
    }

    public function getAllUsers() {
        $users = AdminsModel::column('admin_id,login_name,realname,email','admin_id');
        return $users;
    }
}