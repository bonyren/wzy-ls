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
use app\index\Defs;

class Sms extends Base{
    /*********************短信验证码*********************************/
    public function sendCaptcha($tel, $len=6, $id=''){
        if(!validateMobile($tel)){
            return false;
        }
        //generate code
        $chars = [0,1,2,3,4,5,6,7,8,9];
        $i = 0;
        $code = '';
        while($i<$len){
            $code .= $chars[rand(0, $len-1)];
            $i++;
        }
        $TemplateParam = json_encode(['number'=>$code], JSON_UNESCAPED_UNICODE);
        $TemplateCode = 'SMS_91955085';

        $result = $this->send($TemplateCode, $TemplateParam, $tel);
        if(!$result){
            return false;
        }
        Session::set('CAPTCHA_' . $id, $code);
        return true;
    }
    public function verifyCaptcha($code, $id=''){
        $sessionCode = Session::get('CAPTCHA_' . $id);
        if($sessionCode == $code){
            Session::delete('CAPTCHA_' . $id);
            return true;
        }
        return false;
    }
    /*********************预约订单通知给专家**************************/
    public function sendAppoitOrderToExpert($tel, $time){
        if(!validateMobile($tel)){
            return false;
        }
        $TemplateParam = json_encode(['time'=>$time], JSON_UNESCAPED_UNICODE);
        $TemplateCode = 'SMS_161592455';
        Log::notice('send appoint order sms to expert: ' . $tel);
        $result = $this->send($TemplateCode, $TemplateParam, $tel);
        if(!$result){
            return false;
        }
        return true;
    }
    /*********************预约订单通知给客户**************************/
    public function sendAppoitOrderToCustomer($tel, $time){
        if(!validateMobile($tel)){
            return false;
        }
        $TemplateParam = json_encode(['time'=>$time], JSON_UNESCAPED_UNICODE);
        $TemplateCode = 'SMS_161591265';
        Log::notice('send appoint order sms to customer: ' . $tel);
        $result = $this->send($TemplateCode, $TemplateParam, $tel);
        if(!$result){
            return false;
        }
        return true;
    }
    private function send($TemplateCode, $TemplateParam, $phoneNumber){
        if(!validateMobile($phoneNumber)){
            return false;
        }
        //send sms
        $Action = 'SendSms';
        $SignName = '';
        $AccessKeyId = '';
        $AccessKeySecret = '';

        $SignatureMethod = 'HMAC-SHA1';
        $SignatureVersion = '1.0';
        $SignatureNonce = uniqid(mt_rand(0,0xffff), true);
        $Timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $Format = 'json';
        $Version = '2017-05-25';

        $params = [
            'PhoneNumbers'=>$phoneNumber,
            'SignName'=>$SignName,
            'TemplateCode'=>$TemplateCode,
            'AccessKeyId'=>$AccessKeyId,
            'Action'=>$Action,
            'Format'=>$Format,
            'TemplateParam'=>$TemplateParam,
            'SignatureMethod'=>$SignatureMethod,
            'SignatureNonce'=>$SignatureNonce,
            'SignatureVersion'=>$SignatureVersion,
            'Timestamp'=>$Timestamp,
            'Version'=>$Version
        ];
        ksort($params);
        $sortedQueryStringTmp = "";
        foreach ($params as $key => $value) {
            $sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
        }
        $stringToSign = "GET&" . $this->encode('/') . "&" . $this->encode(substr($sortedQueryStringTmp, 1));
        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $AccessKeySecret . "&",true));
        $Signature = $this->encode($sign);
        $params['Signature'] = $Signature;

        $url = "https://dysmsapi.aliyuncs.com/?Signature={$Signature}{$sortedQueryStringTmp}";
        $responseContent = file_get_contents($url);
        if(!$responseContent){
            Log::error("failed to get response from ali sms, tel:$phoneNumber");
            return false;
        }
        $responseObj = json_decode($responseContent, true);
        if(!$responseObj){
            Log::error("failed to decode ali sms response, tel:$phoneNumber, " . $responseContent);
            return false;
        }
        if(isset($responseObj['Code']) && $responseObj['Code'] == '确定'){
            Log::notice("success to send message to tel:$phoneNumber, template: $TemplateCode");
            return true;
        }
        Log::error("failed to send sms via ali, tel:$phoneNumber, " . $responseContent);
        return false;
    }
    private function encode($str){
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

}