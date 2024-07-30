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
/*
 * CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(200) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `category` tinyint(4) NOT NULL DEFAULT '0',
  `is_read` tinyint(4) NOT NULL DEFAULT '0',
  `read_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `entered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
 */
class Messages extends Base{
    const MESSAGE_APPROVAL_CATEGORY = 1;
    const MESSAGE_ADMIN_SYSTEM_CATEGORY = 2;
    public static $messageCategoryDefs = [
        self::MESSAGE_APPROVAL_CATEGORY=>'审批通知',
        self::MESSAGE_ADMIN_SYSTEM_CATEGORY=>'系统通知'
    ];
    public static $messageCategoryHtmlDefs = [
        self::MESSAGE_APPROVAL_CATEGORY=>'<span class="badge badge-primary">审批通知</span>'
    ];
    protected function __construct(){
        parent::__construct();
    }
    public function load($adminId,
                         $search=[],
                         $page=1,
                         $rows=DEFAULT_PAGE_ROWS,
                         $sort='',
                         $order=''){
        $limit = ($page - 1) * $rows . "," . $rows;
        if($sort == 'category'){
            $order = 'category ' . $order;
        }else{
            $order = 'message_id desc';
        }
        $conditions = ['admin_id'=>$adminId];
        $totalCount = Db::table('messages')->where($conditions)->count();
        $records = Db::table('messages')->where($conditions)->limit($limit)->order($order)->field([
            'message_id',
            'admin_id',
            'title',
            'content',
            'category',
            'is_read',
            'read_time',
            'entered'
        ])->select();
        foreach($records as &$record){
            $record['checked'] = true;
        }
        return [
            'total'=>$totalCount,
            'rows'=>$records
        ];
    }
    public function add($adminId, $category, $title, $content){
        $datas = [
            'admin_id'=>$adminId,
            'title'=>mb_truncateString($title, 200),
            'content'=>$content,
            'category'=>$category
        ];
        $messageId = Db::table('messages')->insertGetId($datas);
        return $messageId;
    }
    public function markRead($messageId){
        Db::table('messages')->where(['message_id'=>$messageId, 'is_read'=>0])->update([
            'is_read'=>1,
            'read_time'=>date('Y-m-d H:i:s')
        ]);
        return true;
    }
    public function markAllRead($adminId){
        Db::table('messages')->where(['admin_id'=>$adminId, 'is_read'=>0])->update(
            [
                'is_read'=>1,
                'read_time'=>Db::raw('now()')
            ]
        );
    }
    public function markSelectedRead($messageIds){
        Db::table('messages')->where(['message_id'=>['in', $messageIds], 'is_read'=>0])->update(
            [
                'is_read'=>1,
                'read_time'=>Db::raw('now()')
            ]
        );
    }
    public function getInfos($messageId){
        $infos = Db::table('messages')->where('message_id', $messageId)->field(true)->find();
        return $infos;
    }
    public function unreadCount($adminId){
        $count = Db::table('messages')->where(['admin_id'=>$adminId, 'is_read'=>0])->count();
        return $count;
    }
}