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
use think\Debug;
use think\Db;
use app\index\logic\Messages as MessagesLogic;
use app\index\logic\Admins as AdminsLogic;

class Messages extends Base
{
    public function __construct(){}
    public function sendApproval($adminId, $title='', $content=''){
        $messagesLogic = MessagesLogic::newObj();

        $messageId = $messagesLogic->add($adminId,
            MessagesLogic::MESSAGE_APPROVAL_CATEGORY,
            empty($title)?MessagesLogic::$messageCategoryDefs[MessagesLogic::MESSAGE_APPROVAL_CATEGORY]:mb_substr($title, 0, 200),
            empty($content)?MessagesLogic::$messageCategoryDefs[MessagesLogic::MESSAGE_APPROVAL_CATEGORY]:$content);
        if(!$messageId){
            Log::error("failed to add message to $adminId");
            return false;
        }
        return true;
    }
    public function sendAdminMessage($title='', $content=''){
        $messagesLogic = MessagesLogic::newObj();
        $adminsLogic = AdminsLogic::newObj();

        $adminIds = $adminsLogic->getAdminIds();
        foreach($adminIds as $adminId) {
            $messageId = $messagesLogic->add($adminId,
                MessagesLogic::MESSAGE_ADMIN_SYSTEM_CATEGORY,
                empty($title) ? MessagesLogic::$messageCategoryDefs[MessagesLogic::MESSAGE_ADMIN_SYSTEM_CATEGORY] : mb_substr($title, 0, 200),
                empty($content) ? MessagesLogic::$messageCategoryDefs[MessagesLogic::MESSAGE_ADMIN_SYSTEM_CATEGORY] : $content);
            if (!$messageId) {
                Log::error("failed to add message to $adminId");
            }
        }
        return true;
    }
}