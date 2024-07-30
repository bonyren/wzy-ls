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
use app\index\model\Lists as ListsModel;
class Lists extends Base{
    public function __construct(){
        parent::__construct();
    }
    public function loadTreeDatas($active = IndexDefs::eListActive){
        $treeDatas = [];
        $rootData = [
            'id'=>0,
            'text'=>'订阅者集合',
            'iconCls'=>'fa fa-cubes',
            'state'=>'open',
            'children'=>[]
        ];
        $conditions = [];
        if($active > 0){
            $conditions['active'] = $active;
        }
        $lists = Db::table('lists')->where($conditions)->field("list_id, name, active")->order('order asc, entered desc')->select();
        foreach ($lists as $key => $list) {
            $listId = $list['list_id'];
            $listName = $list['name'];

            $countData = $this->getListSubscriberCounts($listId);

            $rootData['children'][] = array(
                'id'=>$listId,
                'text'=>$listName,
                'active'=>$list['active'],
                'iconCls'=>'fa fa-cube',
                'state'=>'open',
                'children'=>[],
                'subscriber_count'=>$countData['subscriber_count'],
                'subscriber_blacklisted_count'=>$countData['subscriber_blacklisted_count']
            );
        }
        $treeDatas[] = $rootData;
        $countData = $this->getListSubscriberCounts(-1);
        $treeDatas[] = [
            'id'=>-1,
            'text'=>'未归集',
            'iconCls'=>'fa fa-list',
            'state'=>'open',
            'children'=>[],
            'subscriber_count'=>$countData['subscriber_count'],
            'subscriber_blacklisted_count'=>$countData['subscriber_blacklisted_count']
        ];
        return $treeDatas;
    }
    public function loadLists($search = [],
                                $page=1,
                                $rows=DEFAULT_PAGE_ROWS,
                                $sort = '',
                                $order = ''){
        //排序
        if($sort == 'list_id'){
            $order = 'list_id ' . $order;
        }else if($sort == 'name'){
            $order = 'name ' . $order;
        }else if($sort == 'active'){
            $order = 'active ' . $order;
        }else{
            $order = 'active asc';
        }
        //搜索
        $conditions = [];
        if(!emptyInArray($search, 'name')){
            $conditions['name'] = ['like', "%{$search['name']}%"];
        }
        if(!emptyInArray($search, 'active')){
            $conditions['active'] = (int)$search['active'];
        }
        /////////////////////////////////////////////////
        $totalCount = Db::table('lists')->where($conditions)->count();
        $lists = Db::table('lists')->where($conditions)
            ->page($page, $rows)
            ->order($order)
            ->field(true)
            ->select();
        for($i=0,$count=count($lists); $i<$count; $i++){
            $listId = $lists[$i]['list_id'];
            $countData = $this->getListSubscriberCounts($listId);
            $lists[$i]['subscriber_count'] = $countData['subscriber_count'];
            $lists[$i]['subscriber_blacklisted_count'] = $countData['subscriber_blacklisted_count'];
        }
        return [
            'total'=>$totalCount,
            'rows'=>$lists
        ];
    }
    public function getAllCategoryLists(){
        $lists = Db::table('Lists')->field('list_id, name')->order('order asc')->select();
        return $lists;
    }
    public function loadTreeGridDatas($campaignId=''){
        $checkedLists = [];
        if($campaignId) {
            $checkedLists = Db::table('campaigns_lists')->where('campaign_id', $campaignId)->column('list_id');
        }
        $treeDatas = [];
        $rootData = [
            'id'=>0,
            'text'=>'集合',
            'iconCls'=>'fa fa-cubes',
            'state'=>'open',
            'subscriber_count'=>0,
            'subscriber_blacklisted_count'=>0,
            'checked'=>false,
            'children'=>[]
        ];
        $conditions = [];
        $lists = Db::table('lists')->where($conditions)->field("list_id, name, active")->order('order asc, entered desc')->select();
        foreach ($lists as $key => $list) {
            $listId = $list['list_id'];
            $listName = $list['name'];

            $counts = $this->getListSubscriberCounts($listId);
            $subscriberCount = $counts['subscriber_count'];
            $subscriberBlacklistedCount = $counts['subscriber_blacklisted_count'];

            $rootData['children'][] = array(
                'id'=>$listId,
                'text'=>$listName,
                'list_id'=>$listId,
                'name'=>$listName,
                'active'=>$list['active'],
                'iconCls'=>'fa fa-cube',
                'state'=>'open',
                'subscriber_count'=>$subscriberCount,
                'subscriber_blacklisted_count'=>$subscriberBlacklistedCount,
                'checked'=>in_array($listId, $checkedLists),
                'children'=>[]
            );
        }
        $treeDatas[] = $rootData;
        return $treeDatas;
    }
    public function loadDataGridDatasByCampaign($campaignId){
        $lists = Db::table('campaigns_lists')->alias('CL')->join('lists L', 'CL.list_id=L.list_id')->where(['CL.campaign_id'=>$campaignId])
            ->field('L.list_id, L.name')->select();
        for($i=0,$count=count($lists); $i<$count; $i++){
            $listId = $lists[$i]['list_id'];
            $lists[$i]['id'] = $listId;
            $counts = $this->getListSubscriberCounts($listId);
            $lists[$i]['subscriber_count'] = $counts['subscriber_count'];
            $lists[$i]['subscriber_blacklisted_count'] = $counts['subscriber_blacklisted_count'];
        }
        return $lists;
    }

    /**获取订阅者所属的集合列表
     * @param $subscriberId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function loadListsBySubscriber($subscriberId){
        $lists = Db::table('list_subscribers')->alias('LS')->join('lists L', 'LS.list_id=L.list_id')->where(['LS.subscriber_id'=>$subscriberId])
            ->field('L.list_id, L.name')->select();
        for($i=0,$count=count($lists); $i<$count; $i++){
            $listId = $lists[$i]['list_id'];
            $lists[$i]['id'] = $listId;
            $counts = $this->getListSubscriberCounts($listId);
            $lists[$i]['subscriber_count'] = $counts['subscriber_count'];
            $lists[$i]['subscriber_blacklisted_count'] = $counts['subscriber_blacklisted_count'];
        }
        return $lists;
    }
    /**********************************************************************/
    public function getAllLists(){
        $lists = Db::table('Lists')->field('list_id, name, active')->order('order asc')->select();
        for($i=0,$count=count($lists); $i<$count; $i++){
            $listId = $lists[$i]['list_id'];
            $lists[$i]['id'] = $listId;
            $counts = $this->getListSubscriberCounts($listId);
            $lists[$i]['subscriber_count'] = $counts['subscriber_count'];
            $lists[$i]['subscriber_blacklisted_count'] = $counts['subscriber_blacklisted_count'];

            if($lists[$i]['active'] == IndexDefs::eListInactive){
                //无效
                $lists[$i]['name'] =  $lists[$i]['name'] . '(无效)';
            }
        }
        return $lists;
    }
    public function getOtherLists($listId){
        $lists = Db::table('Lists')->where('list_id', '<>', $listId)
            ->field('list_id, name')
            ->order('order asc')
            ->select();
        return $lists;
    }
	public function getListSubscriberCounts($listId){
        if($listId == -1){
            //未归集
            $subscriber_count = Db::table('subscribers')->alias('S')
                ->join('list_subscribers LS','LS.subscriber_id=S.subscriber_id','LEFT')
                ->where(['LS.list_id' => null, 'S.blacklisted' => 0, 'S.deleted'=>0])
                ->count();

            $subscriber_blacklisted_count = Db::table('subscribers')->alias('S')
                ->join('list_subscribers LS','LS.subscriber_id=S.subscriber_id','LEFT')
                ->where(['LS.list_id' => null, 'S.blacklisted' => 1, 'S.deleted'=>0])
                ->count();
        }else {
            $subscriber_count = Db::table('list_subscribers')->alias('LS')
                ->join('subscribers S', 'LS.subscriber_id=S.subscriber_id')
                ->where(['LS.list_id' => $listId, 'S.blacklisted' => 0, 'S.deleted'=>0])
                ->count();

            $subscriber_blacklisted_count = Db::table('list_subscribers')->alias('LS')
                ->join('subscribers S', 'LS.subscriber_id=S.subscriber_id')
                ->where(['LS.list_id' => $listId, 'S.blacklisted' => 1, 'S.deleted'=>0])
                ->count();
        }
        return [
            'subscriber_count'=>$subscriber_count,
            'subscriber_blacklisted_count'=>$subscriber_blacklisted_count
        ];
    }
    public function addList($formData){
        //$order = Db::table('lists')->min('`order`');
        $order = Db::table('lists')->order('order asc')->value('order');
        if($order === null){
            $order = 0;
        }else{
            $order -= 1;
        }
        $formData['order'] = $order;
        $listsModel = model('Lists');
        $listsModel->data($formData);
        $listsModel->save();
        return true;
    }
    public function updateList($listId, $formData){
        $listsModel = model('Lists');
        $listsModel->save($formData,['list_id'=>$listId]);
        return true;
    }
    public function deleteList($listId){
        $listsModel = ListsModel::get($listId);
        if($listsModel){
            $listsModel->delete();
        }
        Db::table('list_subscribers')->where(['list_id'=>$listId])->delete();
        Db::table('campaigns_lists')->where(['list_id'=>$listId])->delete();
        return true;
    }
    public function updateActive($listId, $isActive){
        $active = $isActive?IndexDefs::eListActive:IndexDefs::eListInactive;
        Db::table('lists')->where('list_id', $listId)->update([
            'active'=>$active
        ]);
        return true;
    }
    public function updateOrder($listId, $upDown){
        $order = Db::table('lists')->where('list_id', $listId)->value('order');
        if($upDown == 1){
            //up
            $upRow = Db::table('lists')->where(['order'=>['<', $order]])->order('order desc')->field('list_id, order')->find();
            if($upRow === null){
                return true;
            }
            $upListId = $upRow['list_id'];
            $upOrder = $upRow['order'];
            Db::table('lists')->where('list_id', $listId)->setField('order', $upOrder);
            Db::table('lists')->where('list_id', $upListId)->setField('order', $order);

        }else if($upDown == -1){
            //down
            $downRow = Db::table('lists')->where(['order'=>['>', $order]])->order('order asc')->field('list_id, order')->find();
            if($downRow === null){
                return true;
            }
            $downListId = $downRow['list_id'];
            $downOrder = $downRow['order'];
            Db::table('lists')->where('list_id', $listId)->setField('order', $downOrder);
            Db::table('lists')->where('list_id', $downListId)->setField('order', $order);
        }
        return true;
    }
    public function getList($listId){
        if($listId == -1){
            return [
                'list_id'=>-1,
                'name'=>'未归集',
                'description'=>'用来归集不在任何集合的订阅者'
            ];
        }
        $listsModel = ListsModel::get($listId);
        if(!$listsModel){
            return null;
        }
        return [
            'list_id'=>$listsModel->list_id,
            'name'=>$listsModel->name,
            'description'=>$listsModel->description
        ];
    }
    public function getListsBySubscriber($subscriberId){
        $lists = Db::table('lists')->alias('L')->join('list_subscribers LS', 'L.list_id=LS.list_id')
            ->where(['LS.subscriber_id'=>$subscriberId])->field('L.list_id, L.name')->select();
        return $lists;
    }
}
?>