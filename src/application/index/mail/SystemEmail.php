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
namespace app\index\mail;
use app\index\Defs as IndexDefs;
use think\Log;
use think\Debug;
use think\Db;
use app\index\logic\Templates as TemplatesLogic;
use app\index\logic\Mailboxes as MailboxesLogic;
use app\index\logic\Subscribers as SubscribersLogic;
use mail\MailMessage;

class SystemEmail extends Base{
    public function __construct(){
        parent::__construct();
    }
    public function sendDirectEmail($email, $subject, $message){
        $hasHtml = strip_tags($message) != $message;
        if ($hasHtml) {
            $message = stripslashes($message);
            $textMessage = MailMessage::html2text($message);
            $htmlMessage = $message;
        } else {
            $textMessage = $message;
            $htmlMessage = $message;
            $htmlMessage = nl2br($htmlMessage);
            $htmlMessage = preg_replace('~https?://[^\s<]+~i', '<a href="$0">$0</a>', $htmlMessage);
        }
        $mailboxesLogic = MailboxesLogic::I();
        $mailboxConfig = $mailboxesLogic->getSystemMailbox();
        if(!$mailboxConfig){
            Log::error("can't find the system mailbox config");
            return false;
        }
        $camelMailer = new CamelMailer();
        $camelMailer->initializeSystem($mailboxConfig);

        if (!empty($htmlMessage)) {
            $camelMailer->add_html($htmlMessage, $textMessage);
            $camelMailer->add_text($textMessage);
        }else {
            $camelMailer->add_text($textMessage);
        }
        $fromName = systemSetting('EMAIL_FROM_NAME');
        //overwritten the default from_email and from_name
        $camelMailer->setFrom($mailboxConfig['from_email'], $fromName);
        $toName = abstractNameFromEmail($email);
        $sendOK = $camelMailer->compatSend($toName, $email, $subject);
        return $sendOK;
    }

}