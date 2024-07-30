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
use think\Debug;
use think\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Mailer extends Base
{
    protected $config = [
        'EMAIL_SMTP' => '',
        'EMAIL_PORT' => 25,
        'EMAIL_USER' => '',
        'EMAIL_PWD' => '',
        'EMAIL_FROM_ADDRESS' => '',
        'EMAIL_FROM_NAME' => '',
    ];
    public $isHtml = false;

    protected function __construct() {
        $this->config = array_merge($this->config, [
            'EMAIL_SMTP' => systemSetting('EMAIL_SMTP'),
            'EMAIL_PORT' => systemSetting('EMAIL_PORT'),
            'EMAIL_USER' => systemSetting('EMAIL_USER'),
            'EMAIL_PWD' => systemSetting('EMAIL_PWD'),
            'EMAIL_FROM_ADDRESS' => systemSetting('EMAIL_FROM_ADDRESS'),
            'EMAIL_FROM_NAME' => systemSetting('EMAIL_FROM_NAME'),
        ]);
    }

    public function sendHtml($from, $to, $subject, $content){
        $phpMailer = new PHPMailer();

        $phpMailer->SingleTo = false;
        $phpMailer->CharSet = 'UTF-8';
        $phpMailer->SMTPDebug = 0;
        $phpMailer->Debugoutput = 'html';
        //initialize the smtp configuration
        $phpMailer->Host = self::SMTP_HOST;
        $phpMailer->Port = self::SMTP_PORT;
        $phpMailer->Username = self::SMTP_ACCOUNT;
        $phpMailer->Password = self::SMTP_PASSWORD;
        $phpMailer->SMTPAuth = true;
        $phpMailer->SMTPSecure = '';
        $phpMailer->SMTPAutoTLS = false;
        $phpMailer->Mailer = 'smtp';
        $phpMailer->Sender = self::SMTP_ACCOUNT;
        $fromSections = explode("@", $from);
        $phpMailer->SetFrom($from, $fromSections[0]);
        $phpMailer->SMTPOptions = [];
        $phpMailer->Subject = $subject;
        $phpMailer->Body = $content;
        $phpMailer->isHTML(true);
        $phpMailer->addAddress($to);
        $result = $phpMailer->send();
        if(!$result){
            $mailError = $phpMailer->ErrorInfo;
        }
        return $result;
    }
    /******************************************************************************************************************/
    const SMTP_HOST = "smtp.exmail.qq.com";
    const SMTP_PORT = 25;
    const SMTP_ACCOUNT = "cs@camelproxy.com";
    const SMTP_PASSWORD = "Welcome123!";
    const SUPPORT_EMAIL = "support@camelproxy.com";
    public function sendToSupport($from, $subject, $content){
        $phpMailer = new PHPMailer();

        $phpMailer->SingleTo = false;
        $phpMailer->CharSet = 'UTF-8';
        $phpMailer->SMTPDebug = 0;
        $phpMailer->Debugoutput = 'html';
        //initialize the smtp configuration
        $phpMailer->Host = self::SMTP_HOST;
        $phpMailer->Port = self::SMTP_PORT;
        $phpMailer->Username = self::SMTP_ACCOUNT;
        $phpMailer->Password = self::SMTP_PASSWORD;
        $phpMailer->SMTPAuth = true;
        $phpMailer->SMTPSecure = '';
        $phpMailer->SMTPAutoTLS = false;
        $phpMailer->Mailer = 'smtp';
        $phpMailer->Sender = self::SMTP_ACCOUNT;
        $fromSections = explode("@", $from);
        $phpMailer->SetFrom($from, $fromSections[0]);
        $phpMailer->SMTPOptions = [];
        $phpMailer->Subject = $subject;
        $phpMailer->Body = $content;
        $phpMailer->isHTML(false);
        $phpMailer->addAddress(self::SUPPORT_EMAIL);
        $result = $phpMailer->send();
        if(!$result){
            $mailError = $phpMailer->ErrorInfo;
            Log::write("failed to send email from {$from} to support, error: {$mailError}", Log::ERROR);
        }
        return $result;
    }
    public function sendToUser($to, $subject, $content){
        $phpMailer = new PHPMailer();

        $phpMailer->SingleTo = false;
        $phpMailer->CharSet = 'UTF-8';
        $phpMailer->SMTPDebug = 0;
        $phpMailer->Debugoutput = 'html';
        //initialize the smtp configuration
        $phpMailer->Host = self::SMTP_HOST;
        $phpMailer->Port = self::SMTP_PORT;
        $phpMailer->Username = self::SMTP_ACCOUNT;
        $phpMailer->Password = self::SMTP_PASSWORD;
        $phpMailer->SMTPAuth = true;
        $phpMailer->SMTPSecure = '';
        $phpMailer->SMTPAutoTLS = false;
        $phpMailer->Mailer = 'smtp';
        $phpMailer->Sender = self::SMTP_ACCOUNT;
        $fromSections = explode("@", self::SMTP_ACCOUNT);
        $phpMailer->SetFrom(self::SMTP_ACCOUNT, $fromSections[0]);
        $phpMailer->SMTPOptions = [];
        $phpMailer->Subject = $subject;
        $phpMailer->Body = $content;
        $phpMailer->isHTML(false);
        $phpMailer->addAddress($to);
        $result = $phpMailer->send();
        if(!$result){
            $mailError = $phpMailer->ErrorInfo;
            Log::write("failed to send email to {$to} from cs, error: {$mailError}", Log::ERROR);
        }
        return $result;
    }
}