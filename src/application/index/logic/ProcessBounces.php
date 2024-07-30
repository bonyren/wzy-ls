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
use think\Debug;
use think\Exception;
use think\Log;
use app\index\Defs as IndexDefs;
use app\index\model\Mailboxes as MailboxesModel;

class ProcessBounces extends Base{
    public function __construct(){}
    public function process(){
        $mailboxes = Db::table('mailboxes')->field(true)->select();
        foreach($mailboxes as $mailbox){
            $mailboxId = $mailbox['mailbox_id'];
            $host = $mailbox['bounce_host'];
            $port = $mailbox['bounce_port'];
            $protocol = $mailbox['bounce_protocol'];
            $secure = $mailbox['bounce_secure'];
            $user = $mailbox['account'];
            $password = $mailbox['password'];
            try {
                if ($protocol == MailboxesModel::eMailboxProtocolPop) {
                    $result = $this->processPop($mailboxId, $host, $port, $user, $password, $secure);
                    if (!$result) {
                        Log::error("failed to processPop, host:{$host}, port:{$port}");
                    }
                } else if ($protocol == MailboxesModel::eMailboxProtocoImap) {
                    $result = $this->processMbox($mailboxId, $host, $port, $user, $password, $secure);
                    if (!$result) {
                        Log::error("failed to processMbox, host:{$host}, port:{$port}");
                    }
                } else {
                    Log::error("can't support the bounce protocol");
                }
            }catch (\Exception $e){
                Log::error("ProcessBounces exception: " . $e->getMessage());
            }
        }
    }
    protected function processPop($mailboxId, $server, $port, $user, $password, $secure){
        set_time_limit(6000);
        if (!$port) {
            $port = '110/pop3/notls';
        }
        $flag = "/pop3";
        if($secure == MailboxesModel::eMailboxSecureSSL){
            $flag .= "/ssl/novalidate-cert";
        }
        if (!TEST) {
            $link = imap_open('{'.$server.':'.$port . $flag . '}INBOX', $user, $password, CL_EXPUNGE);
        } else {
            $link = imap_open('{'.$server.':'.$port . $flag . '}INBOX', $user, $password);
        }
        if (!$link) {
            Log::error("Cannot create POP3 connection to $server: " . imap_last_error());
            return false;
        }
        $result = $this->processMessages($mailboxId, $link, 100000);
        if (!TEST) {
            //CL_EXPUNGE邮箱关闭时自动清除邮箱, 目前不起作用，原因待后续调查
            imap_close($link, CL_EXPUNGE);
        }else{
            imap_close($link);
        }
        return $result;
    }
    protected function processMbox($mailboxId, $server, $port, $user, $password, $secure){
        set_time_limit(6000);
        $flag = "";
        if($secure == MailboxesModel::eMailboxSecureSSL){
            $flag .= "/ssl/novalidate-cert";
        }
        if (!TEST) {
            //CL_EXPUNGE邮箱关闭时自动清除邮箱
            $link = imap_open('{'.$server.':'.$port.$flag.'}INBOX', $user, $password, CL_EXPUNGE);
        } else {
            $link = imap_open('{'.$server.':'.$port.$flag.'}INBOX', $user, $password);
        }
        if (!$link) {
            Log::error("Cannot create IMAP connection to $server: " . imap_last_error());
            return false;
        }
        $result = $this->processMessages($mailboxId, $link, 100000);
        if (!TEST) {
            imap_close($link, CL_EXPUNGE);
        }else{
            imap_close($link);
        }
        return $result;
    }
    protected function processMessages($mailboxId, $link, $max = 3000){
        $num = imap_num_msg($link);
        Log::notice("$num bounces to fetch from the mailbox");
        if ($num == 0) {
            return false;
        }
        if ($num > $max) {
            Log::notice("Processing first $max bounces");
            $num = $max;
        }
        for ($x = 1; $x <= $num; ++$x) {
            set_time_limit(60);
            $header = imap_fetchheader($link, $x);
            if ($x % 25 == 0) {
                Log::notice("$x done");
            }
            $processed = $this->processImapBounce($mailboxId, $link, $x, $header);
            if ($processed) {
                if (!TEST) {
                    Log::notice('Deleting message '. $x);
                    imap_delete($link, $x);
                }
            } else {
                if (!TEST) {
                    Log::notice('Deleting unprocessed message ' . $x);
                    imap_delete($link, $x);
                }
            }
        }
        Log::notice('Closing mailbox, and purging messages');
        if(!TEST){
            imap_expunge($link);
        }
        set_time_limit(60 * $num);
        return true;
    }
    protected function processImapBounce($mailboxId, $link, $num, $header){
        //解析内容
        $headerInfo = imap_headerinfo($link, $num);
        if(!$headerInfo){
            //object
            return false;
        }
        $bounceDate = date('Y-m-d H:i:s', strtotime($headerInfo->date));
        $body = imap_body($link, $num);
        if(!$body){
            return false;
        }
        $bounceBody = $this->decodeBody($header, $body);
        //debug logging
        /*
        Log::notice('bounce email header info: ' . var_export($headerInfo, true));
        Log::notice('bounce email header: ' . $header);
        Log::notice('bounce email body: ' . $bounceBody);
        */
        //定位campaign和subscriber
        $campaignId = $this->findCampaignId($bounceBody);
        $subscriberId = $this->findSubscriberId($bounceBody);

        Log::notice('processImapBounce subscriber id: ' . $subscriberId . ' campaign id: ' . $campaignId);
        //保存
        $result = Db::table('bounces')->insert([
                'date'=>$bounceDate,
                'header'=>$header,
                'data'=>$body,//decode之前的
                'processed_status'=>IndexDefs::eBouncePending,
                'comment'=>'',
                'mailbox_id'=>$mailboxId
            ]
        );
        if(!$result){
            //impossible
            return false;
        }
        $bounceId = Db::table('bounces')->getLastInsID();
        $result = $this->processBounceData($bounceId, $campaignId, $subscriberId);
        return $result;
    }

    protected function processBounceData($bounceId, $campaignId, $subscriberId){
        if ($campaignId === 'system') {
            if($subscriberId) {
                Db::table('bounces')->where(['bounce_id' => $bounceId])->update([
                    'processed_status' => IndexDefs::eBounceSystemSubscriberMessage,
                    'comment' => IndexDefs::$eBounceStatusDefs[IndexDefs::eBounceSystemSubscriberMessage]
                ]);
            }else{
                Db::table('bounces')->where('bounce_id', $bounceId)->update([
                    'processed_status'=>IndexDefs::eBounceSystemUnidentifiedSubscriberMessage,
                    'comment'=>IndexDefs::$eBounceStatusDefs[IndexDefs::eBounceSystemUnidentifiedSubscriberMessage]
                ]);
            }
        } else if ($campaignId && $subscriberId) {
            //# check if we already have this um as a bounce
            //# so that we don't double count "delayed" like bounces
            $exists = Db::table('bounces_campaign_subscriber')
                ->where(['subscriber_id'=>$subscriberId, 'campaign_id'=>$campaignId])->count();
            if (empty($exists)) {
                Db::table('bounces_campaign_subscriber')->insert([
                    'subscriber_id'=>$subscriberId,
                    'campaign_id'=>$campaignId,
                    'bounce_id'=>$bounceId
                ]);
                Db::table('bounces')->where('bounce_id', $bounceId)->update([
                    'processed_status'=>IndexDefs::eBounceCampaignSubscriberMessage,
                    'comment'=>IndexDefs::$eBounceStatusDefs[IndexDefs::eBounceCampaignSubscriberMessage]
                ]);
                Db::table('campaigns')->where('campaign_id', $campaignId)->setInc('bounce_count');
                Db::table('subscribers')->where('subscriber_id', $subscriberId)->setInc('bounce_count');
            } else {
                //# we create the relationship, but don't increase counters
                /*不需要保存，重复退信就简单更新状态就好了
                Db::table('bounces_campaign_subscriber')->insert([
                    'subscriber_id'=>$subscriberId,
                    'campaign_id'=>$campaignId,
                    'bounce_id'=>$bounceId
                ]);*/
                //# we cannot translate this text
                Db::table('bounces')->where('bounce_id', $bounceId)->update([
                    'processed_status'=>IndexDefs::eBounceCampaignSubscriberDuplicateMessage,
                    'comment'=>IndexDefs::$eBounceStatusDefs[IndexDefs::eBounceCampaignSubscriberDuplicateMessage]
                ]);
            }
        } else if ($subscriberId) {
            //campaign不明，不用判断是否存在，直接新增
            Db::table('bounces_campaign_subscriber')->insert([
                'subscriber_id'=>$subscriberId,
                'campaign_id'=>$campaignId,
                'bounce_id'=>$bounceId
            ]);
            Db::table('bounces')->where('bounce_id', $bounceId)->update([
                'processed_status'=>IndexDefs::eBounceUnidentifiedCampaignSubscriberMessage,
                'comment'=>IndexDefs::$eBounceStatusDefs[IndexDefs::eBounceUnidentifiedCampaignSubscriberMessage]
            ]);
            //这种情况下如何判断重复退信?
            Db::table('subscribers')->where('subscriber_id', $subscriberId)->setInc('bounce_count');
        } else if ($campaignId) {
            Db::table('bounces')->where('bounce_id', $bounceId)->update([
                'processed_status'=>IndexDefs::eBounceCampaignUnidentifiedSubscriberMessage,
                'comment'=>IndexDefs::$eBounceStatusDefs[IndexDefs::eBounceCampaignUnidentifiedSubscriberMessage]
            ]);
            //这种情况下如何判断重复退信?
            Db::table('campaigns')->where('campaign_id', $campaignId)->setInc('bounce_count');
        } else {
            Db::table('bounces')->where('bounce_id', $bounceId)->update([
                'processed_status'=>IndexDefs::eBounceUnidentified,
                'comment'=>IndexDefs::$eBounceStatusDefs[IndexDefs::eBounceUnidentified]
            ]);
            return false;
        }
        return true;
    }
    /**************helper function****************/
    /**gmail退信找不到CampaignId
     * @param $bounceBody
     * @return int|string
     */
    private function findCampaignId($bounceBody){
        //找不到campaign id
        $campaignId = 0;
        //?:非捕获分组
        if (preg_match('/(?:CamelList-CampaignId): (.*)\r\n/iU', $bounceBody, $match)) {
            $campaignId = trim($match[1]);
        }
        return $campaignId;
    }

    /**
     *
     * @param $bounceBody
     * @return int|mixed|string
     */
    private function findSubscriberId($bounceBody){
        $subscriberId = 0;
        $user = '';
        if (preg_match('/(?:CamelList-Member): (.*)\r\n/iU', $bounceBody, $match)) {
            $user = trim($match[1]);
        }
        // some versions used the email to identify the users, some the userid and others the uniqid
        // use backward compatible way to find user
        if($user) {
            if (strpos($user, '@') !== false) {
                $subscriberId = Db::table('subscribers')->where('email', $user)->value('subscriber_id');
                if ($subscriberId === null) {
                    $subscriberId = 0;
                }
            } elseif (preg_match("/^\d$/", $user)) {
                $subscriberId = $user;
            } elseif (!empty($user)) {
                $subscriberId = Db::table('subscribers')->where('uniqid', $user)->value('subscriber_id');
                if ($subscriberId === null) {
                    $subscriberId = 0;
                }
            }
        }
        //## if we didn't find any, parse anything looking like an email address and check if it's a subscriber.
        //# this is probably fairly time consuming, but as the process is only done once every so often
        //# that should not be too bad
        if (!$subscriberId) {
            preg_match_all('/[\S]+@[\S\.]+/', $bounceBody, $regs);
            foreach ($regs[0] as $match) {
                $email = $this->cleanEmail($match);
                $subscriberId = Db::table('subscribers')->where('email', $email)->value('subscriber_id');
                if ($subscriberId === null) {
                    $subscriberId = 0;
                }
                if($subscriberId){
                    break;
                }
            }
        }
        return $subscriberId;
    }
    private function decodeBody($header, $body){
        $transfer_encoding = '';
        if (preg_match('/Content-Transfer-Encoding: ([\w-]+)/i', $header, $regs)) {
            $transfer_encoding = strtolower($regs[1]);
        }
        switch ($transfer_encoding) {
            case 'quoted-printable':
                $decoded_body = imap_qprint($body);
                break;
            case 'base64':
                Log::notice("base64 body");
                $decoded_body = imap_base64($body);
                break;
            case '7bit':
            case '8bit':
            default:
                ;//do nothing
        }
        if (!empty($decoded_body)) {
            return $decoded_body;
        } else {
            return $body;
        }
    }
    private function cleanEmail($value){
        $value = trim($value);
        $value = preg_replace("/\r/", '', $value);
        $value = preg_replace("/\n/", '', $value);
        $value = preg_replace('/"/', '&quot;', $value);
        $value = preg_replace('/^mailto:/i', '', $value);
        $value = str_replace('(', '', $value);
        $value = str_replace(')', '', $value);
        $value = preg_replace('/\.$/', '', $value);
        $value = preg_replace('/`/', '&lsquo;', $value);
        $value = stripslashes($value);
        return $value;
    }
}