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
use think\Db;
use think\Log;
use app\index\Defs as IndexDefs;
use app\index\logic\CampaignAttachments as CampaignAttachmentsLogic;
use app\index\service\AppConf;

class Base{
    public function __construct(){}
    //占位符号
    const ePlaceholderEmail = 'EMAIL';
    const ePlaceholderFromEmail = 'FROMEMAIL';
    const ePlaceholderSubject = 'SUBJECT';
    const ePlaceholderContent = 'CONTENT';
    const ePlaceholderFooter = 'FOOTER';
    const ePlaceholderSignature = 'SIGNATURE';
    //生成url
    protected function generateTrackUrl($uid, $campaignId){
        return AppConf::I()->siteUrl() . url('front/Track/image', ['uid'=>$uid, 'cid'=>$campaignId]);
    }
    protected function generateAttachDownloadUrl($campaignAttachId){
        return AppConf::I()->siteUrl() . url('front/Download/downloadCampaignAttach', ['campaignAttachId'=>$campaignAttachId]);
    }

    /**添加附件
     * @param $campaignId
     * @param $mailer
     * @param $type
     * @return bool
     * @throws \think\Exception
     */
    protected  function addAttachments($campaignId, &$mailer, $type){
        $hasError = false;
        $totalSize = 0;
        $campaignAttachmentsLogic = CampaignAttachmentsLogic::I();
        $attachments = $campaignAttachmentsLogic->getAttachments($campaignId);
        if(empty($attachments)){
            return true;
        }
        if ($type == 'text') {
            $mailer->append_text('This message contains attachments that can be viewed with a web browser:'."\n");
        }
        foreach($attachments as $attachment){
            $totalSize += $attachment['size'];
            if($hasError){
                break;
            }
            switch ($type) {
                case 'html':
                    $diskFullPath = convertUploadSaveName2DiskFullPath($attachment['save_name']);
                    if (is_file($diskFullPath) && filesize($diskFullPath)) {
                        $fp = fopen($diskFullPath, 'r');
                        if ($fp) {
                            $contents = fread($fp,filesize($diskFullPath));
                            fclose($fp);
                            $mailer->add_attachment($contents, $attachment['original_name'], $attachment['mime_type']);
                        }
                    } else {
                        logEvent("附件 {$diskFullPath} 不存在");
                        $hasError = true;
                    }
                    break;
                case 'text':
                    $mailer->append_text($attachment['description']."\n".'Location'.': '. $this->generateAttachDownloadUrl($attachment['campaign_attach_id']) ."\n");
                    break;
            }
        }
        //# keep track of an error count, when sending the queue
        if ($hasError) {
            Db::table('campaigns')->where('campaign_id', $campaignId)->update(['status'=>IndexDefs::eCampaignStatusSuspended]);
            logEvent("Campaign $campaignId suspended errors with attachments");
        }
        return !$hasError;
    }

    /**清理url
     * @param $url
     * @param array $disallowed_params
     * @return string
     */
    protected function cleanUrl($url, $disallowed_params = array('PHPSESSID')){
        $parsed = @parse_url($url);
        $params = array();
        if (empty($parsed['query'])) {
            $parsed['query'] = '';
        }
        if (strpos($parsed['query'], '&amp;')) {
            $pairs = explode('&amp;', $parsed['query']);
            foreach ($pairs as $pair) {
                if (strpos($pair, '=') !== false) {
                    list($key, $val) = explode('=', $pair);
                    $params[$key] = $val;
                } else {
                    $params[$pair] = '';
                }
            }
        } else {
            //# parse_str turns . into _ which is wrong
            $params = $this->parseQueryString($parsed['query']);
        }
        $uri = !empty($parsed['scheme']) ? $parsed['scheme'].':'.((strtolower($parsed['scheme']) == 'mailto') ? '' : '//') : '';
        $uri .= !empty($parsed['user']) ? $parsed['user'].(!empty($parsed['pass']) ? ':'.$parsed['pass'] : '').'@' : '';
        $uri .= !empty($parsed['host']) ? $parsed['host'] : '';
        $uri .= !empty($parsed['port']) ? ':'.$parsed['port'] : '';
        $uri .= !empty($parsed['path']) ? $parsed['path'] : '';
        $query = '';
        foreach ($params as $key => $val) {
            if (!in_array($key, $disallowed_params)) {
                $query .= $key.($val != '' ? '='.$val.'&' : '&');
            }
        }
        $query = substr($query, 0, -1);
        $uri .= $query ? '?'.$query : '';
        $uri .= !empty($parsed['fragment']) ? '#'.$parsed['fragment'] : '';

        return $uri;
    }
    protected function parseQueryString($str){
        if (empty($str)) {
            return array();
        }
        $op = array();
        $pairs = explode('&', $str);
        foreach ($pairs as $pair) {
            if (strpos($pair, '=') !== false) {
                list($k, $v) = array_map('urldecode', explode('=', $pair));
                $op[$k] = $v;
            } else {
                $op[$pair] = '';
            }
        }
        return $op;
    }
}