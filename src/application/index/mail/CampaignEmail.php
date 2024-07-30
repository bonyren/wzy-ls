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
use app\index\logic\Campaigns as CampaignsLogic;
use app\index\logic\Mailboxes as MailboxesLogic;
use app\index\logic\Subscribers as SubscribersLogic;
use mail\MailMessage;
use hash\Uuid;
use app\index\logic\Init as InitLogic;
use app\index\service\AppConf;
class CampaignEmail extends Base{
    public function __construct(){
        parent::__construct();
    }
    public function sendCampaignMail($campaignId, $email, $sendTest=false){
        /******************************************************************/
        $campaignsLogic = CampaignsLogic::I();
        /******************************************************************/
        if(!isset(CampaignsLogic::$campaignCaches[$campaignId])){
            if(!$campaignsLogic->preCacheCampaign($campaignId)){
                logEvent('Error loading campaign '.$campaignId.'  in cache');
                return false;
            }
        }else{
            Log::notice("Using the cached campaign, campaign id: $campaignId");
        }
        $campaignCacheData = CampaignsLogic::$campaignCaches[$campaignId];
        $mailboxId = $campaignCacheData['mailbox_id'];

        $mailboxConfig = MailboxesLogic::I()->getMailboxInfos($mailboxId);
        if(!$mailboxConfig){
            Log::error("can't find the mailbox config, id: " . $mailboxId);
            return false;
        }
        /***************************************************************************************************************/
        Log::notice("Sending campaign {$campaignId} with subject " . $campaignCacheData['subject'] . " to {$email}");
        $messageInfos = $this->generateCampaignMessage($mailboxConfig, $campaignId, $email, $sendTest);
        if(!$messageInfos){
            Log::error("failed to generate campaign message for " . $campaignId);
            return false;
        }
        /***************************************************************************************************************/
        // build the email
        $camelMailer = new CamelMailer();
        $camelMailer->initializeCampaign($mailboxConfig, $campaignId);
        $camelMailer->addCustomHeader('CamelList-Owner: <mailto:'.systemSetting('ADMIN_EMAIL').'>');

        $sendHtmlEmail = $messageInfos['htmlPref'] && $campaignCacheData['send_format'] == IndexDefs::eCampaignSendFormatHtml;
        if($sendHtmlEmail){
            $camelMailer->add_html($messageInfos['htmlMessage'], $messageInfos['textMessage']);
            if(!$this->addAttachments($campaignId, $camelMailer, 'html')){
                Log::error("failed to add attachments to $campaignId");
                return false;
            }
        }else{
            $camelMailer->add_text($messageInfos['textMessage']);
            if(!$this->addAttachments($campaignId, $camelMailer, 'text')){
                Log::error("failed to add attachments to $campaignId");
                return false;
            }
        }
        $subject = $campaignCacheData['subject'];
        $toName = abstractNameFromEmail($email);
        $sendOK = $camelMailer->compatSend($toName, $email, $subject);
        if (!$sendOK) {
            Log::error("Error sending campaign $campaignId to $email");
            return false;
        }
        if (!empty($camelMailer->mailsize)) {
            $sizeName = $messageInfos['htmlPref'] ? 'html_size' : 'text_size';
            if(!isset($campaignCacheData[$sizeName])) {
                $campaignsLogic->setCampaignData($campaignId, $sizeName, $camelMailer->mailsize);
                $campaignCacheData[$sizeName] = $camelMailer->mailsize;
            }
        }
        if(!$sendTest){
            if($sendHtmlEmail){
                Db::table('campaigns')->where('campaign_id', $campaignId)->setInc('send_html_count');
            }else{
                Db::table('campaigns')->where('campaign_id', $campaignId)->setInc('send_text_count');
            }
            //修改链接跟踪中的total
            Db::table('link_track_campaign')->where('campaign_id', $campaignId)->setInc('total');

            if (defined('MAX_MAILSIZE') && isset($campaignCacheData['html_size']) && $campaignCacheData['html_size'] > MAX_MAILSIZE) {
                logEvent(sprintf("Message too large (%s is over %s), suspending", $campaignCacheData['html_size'], MAX_MAILSIZE));
                logEvent("Campaign $campaignId suspended. Message too large");
                Db::table('campaigns')->where('campaign_id', $campaignId)->update(['status'=>IndexDefs::eCampaignStatusSuspended]);
            }
        }
        return $sendOK;
    }

    public function generateCampaignMessage(&$mailboxConfig, $campaignId, $email, $sendTest=false){
        /******************************************************************/
        $campaignsLogic = CampaignsLogic::I();
        $subscribersLogic = new SubscribersLogic();
        /******************************************************************/
        if(!isset(CampaignsLogic::$campaignCaches[$campaignId])){
            if(!$campaignsLogic->preCacheCampaign($campaignId)){
                logEvent('Error loading campaign '.$campaignId.'  in cache');
                return false;
            }
        }else{
            Log::notice("Using the cached campaign, campaign id: $campaignId");
        }
        $campaignCacheData = CampaignsLogic::$campaignCaches[$campaignId];
        /*******************************************************************/
        if($sendTest){
            $subscriberId = 0;
            $uid=0;
            $htmlPref = true;
            $subject = $campaignCacheData['subject'];
            $htmlFooter = $campaignCacheData['html_footer'];
            $textFooter = $campaignCacheData['text_footer'];
        }else{
            $subscribersLogic->refreshSubscriberUuid($email);
            $subscriberData = $subscribersLogic->getSubscriberByEmail($email);
            if (!$subscriberData) {
                Log::error("failed to load subscriber data by email: " . $email);
                return false;
            }
            $subscriberId = $subscriberData['subscriber_id'];
            $uid = $subscriberData['uniqid'];
            $htmlPref = $subscriberData['html_email'] == IndexDefs::eSubscriberHtmlEmail ? true : false;
            $subject = $campaignCacheData['subject'];
            $htmlFooter = $campaignCacheData['html_footer'];
            $textFooter = $campaignCacheData['text_footer'];
        }
        /******************************************************************/
        Log::notice("Sending campaign {$campaignId} with subject " . $campaignCacheData['subject'] . " to {$email}");
        $messageContent = $campaignCacheData['message_content'];
        /******************************************************************/
        $html = [];
        $text = [];
        $html[self::ePlaceholderEmail] = $email;
        $text[self::ePlaceholderEmail] = $email;

        $html[self::ePlaceholderFromEmail] = $mailboxConfig['from_email'];
        $text[self::ePlaceholderFromEmail] = $mailboxConfig['from_email'];

        $html[self::ePlaceholderSubject] = $subject;
        $text[self::ePlaceholderSubject] = $subject;

        /******************************************************************/
        $text[self::ePlaceholderFooter] = $textFooter;
        $html[self::ePlaceholderFooter] = $htmlFooter;

        $html[self::ePlaceholderSignature] = "\n\n-- powered by CamelList --\n\n";
        $text[self::ePlaceholderSignature] = "-- powered by CamelList --";

        /******************************************************************/
        if($campaignCacheData['send_format'] == IndexDefs::eCampaignSendFormatHtml){
            $textContent = MailMessage::html2text($messageContent);
            $htmlContent = $messageContent;
        }else{
            $textContent = $messageContent;
            $htmlContent = MailMessage::text2html($messageContent);
        }
        //有模版,template需要加载，只有html需要加载
        if($campaignCacheData['template_id']){
            $htmlMessage = str_replace('[' . self::ePlaceholderContent . ']', $htmlContent, $campaignCacheData['template']);
        }else{
            //no template used
            $htmlMessage = $htmlContent;
        }
        $textMessage = $textContent;
        /**********************************************************************************/
        if (stripos($htmlMessage, '[' . self::ePlaceholderFooter . ']') !== false) {
            $htmlMessage = str_ireplace('[' . self::ePlaceholderFooter . ']', $html[self::ePlaceholderFooter], $htmlMessage);
        } elseif ($html[self::ePlaceholderFooter]) {
            $htmlMessage = $this->addHTMLFooter($htmlMessage, '<br />'.$html[self::ePlaceholderFooter]);
        }
        if (stripos($textMessage, '[' . self::ePlaceholderFooter . ']') !== false) {
            $textMessage = str_ireplace('[' . self::ePlaceholderFooter . ']', $text[self::ePlaceholderFooter], $textMessage);
        } else {
            $textMessage .= "\n\n".$text[self::ePlaceholderFooter];
        }
        /*********************************************************************************/
        if (stripos($htmlMessage, '[' . self::ePlaceholderSignature . ']') !== false) {
            $htmlMessage = str_ireplace('[' . self::ePlaceholderSignature . ']', $html[self::ePlaceholderSignature], $htmlMessage);
        } else {
            $htmlMessage = $this->addHTMLFooter($htmlMessage, '' .$html[self::ePlaceholderSignature]);
        }
        if (stripos($textMessage, '[' . self::ePlaceholderSignature . ']')) {
            $textMessage = str_ireplace('[' . self::ePlaceholderSignature . ']', $text[self::ePlaceholderSignature], $textMessage);
        } else {
            $textMessage .= "\n".$text[self::ePlaceholderSignature];
        }
        //add tracking for html message
        if(!$sendTest) {
            if (stripos($htmlMessage, '</body>')) {
                $htmlMessage = str_replace('</body>',
                    '<img src="' . $this->generateTrackUrl($uid, $campaignId) . '" width="1" height="1" border="0" alt="" /></body>',
                    $htmlMessage);
            } else {
                $htmlMessage .= '<img src="' . $this->generateTrackUrl($uid, $campaignId) . '" width="1" height="1" border="0" alt="" />';
            }
        }
        /*********************************************************************************/
        $htmlMessage = $this->parsePlaceHolders($htmlMessage, $html);
        $textMessage = $this->parsePlaceHolders($textMessage, $text);
        if(!$sendTest) {
            $htmlMessage = $this->makeHtmlLinkTracking($campaignId, $subscriberId, $htmlMessage);
            $textMessage = $this->makeTextLinkTracking($campaignId, $subscriberId, $textMessage);
        }

        preg_match_all('/\[.*\%\%([^\]]+)\]/Ui', $htmlMessage, $matches);
        for ($i = 0; $i < count($matches[0]); ++$i) {
            $htmlMessage = str_ireplace($matches[0][$i], $matches[1][$i], $htmlMessage);
        }
        preg_match_all('/\[.*\%\%([^\]]+)\]/Ui', $textMessage, $matches);
        for ($i = 0; $i < count($matches[0]); ++$i) {
            $textMessage = str_ireplace($matches[0][$i], $matches[1][$i], $textMessage);
        }
        // check that the HTML message as proper <head> </head> and <body> </body> tags
        // some readers fail when it doesn't
        if (!preg_match('#<body.*</body>#ims', $htmlMessage)) {
            $htmlMessage = '<body>'.$htmlMessage.'</body>';
        }
        if (!preg_match('#<head.*</head>#ims', $htmlMessage)) {
            $defaultStyle = '';
            $htmlMessage = '<head><meta content="text/html;charset=utf-8" http-equiv="Content-Type"><title></title>' .$defaultStyle.'</head>'.$htmlMessage;
        }
        if (!preg_match('#<html.*</html>#ims', $htmlMessage)) {
            $htmlMessage = '<html>'.$htmlMessage.'</html>';
        }

        //# remove trailing code after </html>
        $htmlMessage = preg_replace('#</html>.*#msi', '</html>', $htmlMessage);

        //# the editor sometimes places <p> and </p> around the URL
        $htmlMessage = str_ireplace('<p><!DOCTYPE', '<!DOCTYPE', $htmlMessage);
        $htmlMessage = str_ireplace('</html></p>', '</html>', $htmlMessage);
        return [
            'htmlMessage'=>$htmlMessage,
            'textMessage'=>$textMessage,
            'htmlPref'=>$htmlPref
        ];
    }
    protected function addHTMLFooter($message, $footer){
        if (preg_match('#</body>#i', $message)) {
            $message = preg_replace('#</body>#i', $footer.'</body>', $message);
        } else {
            $message .= $footer;
        }
        return $message;
    }
    protected function parsePlaceHolders($content, $array = array()){
        //# the editor turns all non-ascii chars into the html equivalent so do that as well
        foreach ($array as $key => $val) {
            $array[strtoupper($key)] = $val;
            $array[htmlentities(strtoupper($key), ENT_QUOTES, 'UTF-8')] = $val;
            $array[str_ireplace(' ', '&nbsp;', strtoupper($key))] = $val;
        }
        foreach ($array as $key => $val) {
            if (stripos($content, '['.$key.']') !== false) {
                $content = str_ireplace('['.$key.']', $val, $content);
            }
            if (preg_match('/\['.$key.'%%([^\]]+)\]/i', $content, $regs)) {
                if (!empty($val)) {
                    $content = str_ireplace($regs[0], $val, $content);
                } else {
                    $content = str_ireplace($regs[0], $regs[1], $content);
                }
            }
        }
        return $content;
    }

    protected function makeHtmlLinkTracking($campaignId, $subscriberId, $htmlMessage){
        $trackingUrlRoot = AppConf::I()->siteUrl() . url('front/Track/tracking');

        preg_match_all('/<a (.*)href=["\'](.*)["\']([^>]*)>(.*)<\/a>/Umis', $htmlMessage, $links);
        for ($i = 0; $i < count($links[2]); ++$i) {
            $link = $this->cleanUrl($links[2][$i]);
            $link = str_replace('"', '', $link);
            if (preg_match('/\.$/', $link)) {
                $link = substr($link, 0, -1);
            }
            $linkText = $links[4][$i];
            $linkText = strip_tags($linkText);

            $looksLikePublishing = stripos($linkText, 'https://') !== false || stripos($linkText, 'http://') !== false;
            if (!$looksLikePublishing && preg_match('/^http|ftp/i', $link)  &&
                0 !== strpos($link, $trackingUrlRoot) &&
                0 !== strpos($link, AppConf::I()->siteUrl())) {
                $url = $this->cleanUrl($link, array('PHPSESSID', 'uid'));
                $linkTrackId = $this->prepareTrackingDb($campaignId, $url, $link);
                $trackingId = 'H' . '|' . $linkTrackId . '|' . $campaignId . '|' . $subscriberId ^ initSetting(InitLogic::XORMASK);
                $trackingId = base64_encode($trackingId);

                $newLink = sprintf('<a %shref="%s" %s>%s</a>', $links[1][$i],
                    AppConf::I()->siteUrl() . url('front/Track/tracking', ['trackingId'=>$trackingId]),
                    $links[3][$i],
                    $links[4][$i]);

                $htmlMessage = str_replace($links[0][$i], $newLink, $htmlMessage);
            }
        }
        return $htmlMessage;
    }
    protected function makeTextLinkTracking($campaignId, $subscriberId, $textMessage){
        preg_match_all('#(https?://[^\s\>\}\,]+)#mis', $textMessage, $links);
        //# sort the results in reverse order, so that they are replaced correctly
        rsort($links[1]);
        $newLinks = array();

        for ($i = 0; $i < count($links[1]); ++$i) {
            $link = $this->cleanUrl($links[1][$i]);
            if (preg_match('/\.$/', $link)) {
                $link = substr($link, 0, -1);
            }
            if (preg_match('/^http|ftp/i', $link)) {
                $url = $this->cleanUrl($link, array('PHPSESSID', 'uid'));
                $linkTrackId = $this->prepareTrackingDb($campaignId, $url, $link);
                $trackingId = 'T' . '|' . $linkTrackId . '|' . $campaignId . '|' . $subscriberId ^ initSetting(InitLogic::XORMASK);
                $trackingId = base64_encode($trackingId);
                $newLink = AppConf::I()->siteUrl() . url('front/Track/tracking', ['trackingId'=>$trackingId]);
                $newLinks[$linkTrackId] = $newLink;
                $textMessage = str_replace($links[1][$i], '[%%%'.$linkTrackId.'%%%]', $textMessage);
            }
        }
        foreach ($newLinks as $linkTrackId => $newLink) {
            $textMessage = str_replace('[%%%'.$linkTrackId.'%%%]', $newLink, $textMessage);
        }
        return $textMessage;
    }
    protected function prepareTrackingDb($campaignId, $url, $link){
        $linkTrack = Db::table('link_tracks')->where('url', $url)->field('link_track_id,url,uuid,personalise')->find();
        if($linkTrack === null){
            //新增
            $personalise = preg_match('/uid=/', $link);
            $uuid = (string)Uuid::generate(4);
            $linkTrackId = Db::table('link_tracks')->insertGetId([
                'url'=>$url,
                'uuid'=>$uuid,
                'personalise'=>$personalise
            ]);
            $linkTrackUuid = $uuid;
        }else{
            $linkTrackId = $linkTrack['link_track_id'];
            $linkTrackUuid = $linkTrack['uuid'];
        }
        $existCount = Db::table('link_track_campaign')->where(['campaign_id'=>$campaignId, 'link_track_id'=>$linkTrackId])->count();
        if(!$existCount){
            //新增, 发送成功后，修改total
            Db::table('link_track_campaign')->insert([
                'total'=>0,
                'campaign_id'=>$campaignId,
                'link_track_id'=>$linkTrackId
            ]);
        }
        return $linkTrackId;
    }
}