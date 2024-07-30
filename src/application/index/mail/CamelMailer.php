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
use think\Log;
use think\Debug;
use think\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use app\index\service\Setting;
use app\index\service\AppConf;
use app\index\model\Mailboxes as MailboxesModel;

class CamelMailer extends PHPMailer{
    public $image_types = array(
        'gif'  => 'image/gif',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpe'  => 'image/jpeg',
        'bmp'  => 'image/bmp',
        'png'  => 'image/png',
        'tif'  => 'image/tiff',
        'tiff' => 'image/tiff',
        'swf'  => 'application/x-shockwave-flash',
    );

    public $word_wrap = 75;
    public $encoding = 'base64';
    public $le = "\n";

    public $campaign_id = 0;
    public $timestamp = '';
    public $is_blast = false;
    public $is_initialized = false;

    public function __construct($exceptions = false){
        parent::__construct($exceptions);
    }
    protected function initialize(&$mailboxConfig){
        $this->addCustomHeader('CamelList-Version: '.VERSION);
        /*amazon SES doesn't like this
        if (!USE_AMAZONSES && USE_PRECEDENCE_HEADER) {
            $this->addCustomHeader('Precedence: bulk');
        }*/
        $this->addCustomHeader('Precedence: bulk');
        $this->SingleTo = false;
        $this->CharSet = 'UTF-8';
        $this->SMTPDebug = 0;
        $this->Debugoutput = 'html';
        $domain = AppConf::I()->siteDomain();
        if(!$domain){
            $domain = '127.0.0.1';
        }
        $this->Helo = $domain;
        $this->Hostname = $domain;
        //initialize the smtp configuration
        $this->Host = $mailboxConfig['smtp_host'];
        $this->Port = $mailboxConfig['smtp_port'];
        $this->Username = $mailboxConfig['account'];
        $this->Password = $mailboxConfig['password'];
        $this->SMTPAuth = true;
        $this->Mailer = 'smtp';
        $this->Sender = $mailboxConfig['account'];
        $this->addCustomHeader('Bounces-To: ' . $mailboxConfig['account']);
        $this->SetFrom($mailboxConfig['from_email'], $mailboxConfig['from_name']);

        $this->SMTPAutoTLS = true;
        if($mailboxConfig['smtp_secure'] != MailboxesModel::eMailboxSecureFalse){
            $this->SMTPSecure = MailboxesModel::$eMailboxSecureDefs[$mailboxConfig['smtp_secure']];
        }else{
            $this->SMTPSecure = '';
            $this->SMTPAutoTLS = false;
        }
        $this->SMTPOptions = [];
        /////////////////////////////////////////////////////////////////////////
        if(!Request::instance()->isCli()){
            $this->add_timestamp();
        }
        $this->is_initialized = true;
        return true;
    }
    public function initializeCampaign(&$mailboxConfig, $campaignId){
        $this->campaign_id = $campaignId;
        $this->is_blast = true;
        return $this->initialize($mailboxConfig);
    }
    public function initializeSystem(&$mailboxConfig){
        $this->campaign_id = 0;
        $this->is_blast = false;
        return $this->initialize($mailboxConfig);
    }
    /******************************************************************************************************************/
    public function add_html($html, $text = ''){
        $this->Body = $html;
        $this->IsHTML(true);
        if ($text) {
            $this->add_text($text);
        }
        $this->Encoding = 'quoted-printable';
        $this->find_html_images();
    }

    public function add_text($text){
        if (!$this->Body) {
            $this->IsHTML(false);
            $this->Body = html_entity_decode($text, ENT_QUOTES, 'UTF-8'); //$text;
        } else {
            $this->AltBody = html_entity_decode($text, ENT_QUOTES, 'UTF-8'); //$text;
        }
    }

    public function append_text($text){
        if ($this->AltBody) {
            $this->AltBody .= html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        } else {
            $this->Body .= html_entity_decode($text."\n", ENT_QUOTES, 'UTF-8');
        }
    }

    public function add_timestamp(){
        $remoteIp = request()->server('REMOTE_ADDR', '');
        $remoteHost = request()->host();
        $requestTime = request()->time();
        if (!empty($remoteHost)) {
            $ipDomain = $remoteHost;
        }else{
            $ipDomain = gethostbyaddr($remoteIp);
        }
        if ($ipDomain != $remoteIp) {
            $from = "$ipDomain [$remoteIp]";
        }else{
            $from = "[$remoteIp]";
        }
        $hostname = request()->server('HTTP_HOST');
        $requestTime = date('r', $requestTime);
        $sTimeStamp = "from $from by $hostname with HTTP; $requestTime";
        $this->timestamp = $sTimeStamp;
    }
    //override
    public function createHeader(){
        $parentHeader = parent::createHeader();
        if($this->timestamp) {
            $header = 'Received: ' . $this->timestamp . $this->le . $parentHeader;
        } else {
            $header = $parentHeader;
        }
        return $header;
    }
    //override
    public function createBody(){
        $body = parent::createBody();
        return $body;
    }

    public function compatSend($toName, $toAddr, $subject = '') {
        if($this->is_blast) {
            $this->addCustomHeader("CamelList-CampaignId: {$this->campaign_id}");
        }else{
            $this->addCustomHeader("CamelList-CampaignId: system");
        }
        $this->addCustomHeader("CamelList-Member: {$toAddr}");
        if(defined('DEVELOPER_EMAIL') && !empty(DEVELOPER_EMAIL)){
            $this->AddAddress(DEVELOPER_EMAIL);
            if (DEVELOPER_EMAIL != $toAddr) {
                $this->Body = 'X-Originally to: '.$toAddr."\n\n".$this->Body;
            }
        }else{
            $this->AddAddress($toAddr);
        }
        $this->Subject = $subject;
        if ($this->Body) {
            if (!$this->send()) {
                logEvent("Error sending email to {$toName} {$toAddr}");
                //logEvent("Error sending email to {$toName} {$toAddr}" . $this->ErrorInfo);
				Log::error("Error sending email to {$toName} {$toAddr}" . $this->ErrorInfo);
                return false;
            }
        } else {
            logEvent("Error, empty message-body sending email to {$toName} {$toAddr}");
            return false;
        }
        return true;
    }

    public function add_attachment($contents, $filename, $mimeType){
        $this->AddStringAttachment($contents, $filename, 'base64', $mimeType);
    }

    protected function find_html_images(){
        $extensions = array_keys($this->image_types);
        $filesystem_images = array();
        preg_match_all('/"([^"]+\.('.implode('|', $extensions).'))"/Ui', $this->Body, $images);

        for ($i = 0; $i < count($images[1]); ++$i) {
            //# addition for filesystem images
            if ($this->filesystem_image_exists($images[1][$i])) {
                $filesystem_images[] = $images[1][$i];
                $this->Body = str_replace($images[1][$i], basename($images[1][$i]), $this->Body);
            }
            //# end addition
        }
        //# addition for filesystem images
        if (!empty($filesystem_images)) {
            // If duplicate images are embedded, they may show up as attachments, so remove them.
            $filesystem_images = array_unique($filesystem_images);
            sort($filesystem_images);
            for ($i = 0; $i < count($filesystem_images); ++$i) {
                if ($image = $this->get_filesystem_image($filesystem_images[$i])) {
                    $content_type = $this->image_types[strtolower(substr($filesystem_images[$i], strrpos($filesystem_images[$i], '.') + 1))];
                    $cid = $this->add_html_image($image, basename($filesystem_images[$i]), $content_type);
                    if (!empty($cid)) {
                        $this->Body = str_replace(basename($filesystem_images[$i]), "cid:$cid", $this->Body); //@@@
                    }
                }
            }
        }
        //# end addition
    }

    protected function add_html_image($contents, $name = '', $content_type = 'application/octet-stream')
    {
        /* @@TODO additional optimisation:
         *
         * - we store the image base64 encoded
         * - then we decode it to pass it back to phpMailer
         * - when then encodes it again
         * - best would be to take out a step in there, but that would require more modifications
         * to phpMailer
         */

        $cid = bin2hex(random_bytes(16)); // @TODO seems this does not need to be random, just unique? perhaps hash($contents)
        if (method_exists($this, 'AddEmbeddedImageString')) {
            $this->AddEmbeddedImageString(base64_decode($contents), $cid, $name, $this->encoding, $content_type);
        } elseif (method_exists($this, 'AddStringEmbeddedImage')) {
            //# PHPMailer 5.2.5 and up renamed the method
            //# https://github.com/PHPMailer/PHPMailer/issues/42#issuecomment-16217354
            $this->AddStringEmbeddedImage(base64_decode($contents), $cid, $name, $this->encoding, $content_type);
        } elseif (isset($this->attachment) && is_array($this->attachment)) {
            // Append to $attachment array
            $cur = count($this->attachment);
            $this->attachment[$cur][0] = base64_decode($contents);
            $this->attachment[$cur][1] = ''; //$filename;
            $this->attachment[$cur][2] = $name;
            $this->attachment[$cur][3] = 'base64';
            $this->attachment[$cur][4] = $content_type;
            $this->attachment[$cur][5] = true; // isStringAttachment
            $this->attachment[$cur][6] = 'inline';
            $this->attachment[$cur][7] = $cid;
        } else {
            Log::error('phpMailer needs patching to be able to use inline images from templates');
            return false;
        }
        return $cid;
    }

    //# addition for filesystem images

    protected function filesystem_image_exists($filename)
    {
        //#  find the image referenced and see if it's on the server
        $elements = parse_url($filename);
        if($elements === false){
            return false;
        }
        if(isset($elements['scheme'])){
            //绝对url, 不是客户在系统内上次的图片
            return false;
        }
        $localFullPath = convertUploadRelativeUrl2DiskFullPath($filename);
        if(is_file($localFullPath)){
            return true;
        }else{
            return false;
        }
    }

    protected function get_filesystem_image($filename)
    {
        $localFullPath = convertUploadRelativeUrl2DiskFullPath($filename);
        if(is_file($localFullPath)){
            return base64_encode(file_get_contents($localFullPath));
        }else{
            return '';
        }
    }
    protected function EncodeFile($path, $encoding = 'base64'){
        // as we already encoded the contents in $path, return $path
        return chunk_split($path, 76, $this->le);
    }
}
