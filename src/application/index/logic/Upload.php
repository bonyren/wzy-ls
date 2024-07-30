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
use think\Log;
use app\index\model\Attachments;
use app\Defs;
use app\index\Defs as IndexDefs;

class Upload extends Base
{
    /*1-99投资项目使用*/

    /*100-199基金使用*/
    const ATTACH_FUND_PARTNER_AGREEMENT = 100;//基金合伙协议
    public static $attachTypeDefs = [
        self::ATTACH_FUND_PARTNER_AGREEMENT=>['entity_type'=>IndexDefs::ENTITY_FUND,'label'=>'基金合伙人协议'],
    ];
    protected function __construct(){
        parent::__construct();
    }
    public function insertAttach(
        $originalName,
        $saveName,
        $mimeType,
        $fileSize,
        $description,
        $attachmentType,
        $externalId,
        $externalId2=0,
        $pid = 0
    ){
        $attachmentsModel = new Attachments;
        $attachmentsModel->data([
            'original_name'=>$originalName,
            'save_name'=>$saveName,
            'mime_type'=>$mimeType,
            'description'=>$description,
            'size'=>$fileSize,
            'attachment_type'=>$attachmentType,
            'external_id'=>$externalId,
            'external_id2'=>intval($externalId2),
            'pid'=>$pid,
            'entered'=>Db::raw('now()'),
        ]);
        $attachmentsModel->save();
        $attachmentId = $attachmentsModel->attachment_id;
        Log::notice("the new attachment id: " . $attachmentId);
        return $attachmentId;
    }
    public function getAttaches($attachmentType, $externalId, $externalId2=0){
        if (empty($externalId)) {
            return [];
        }
        $conditions = [
            'attachment_type'=>$attachmentType,
            'external_id'=>$externalId,
            'status'=>IndexDefs::ATTACHMENT_OK
        ];
        if ($externalId2) {
            $conditions['external_id2'] = $externalId2;
        }
        $attaches = Db::table('attachments')->where($conditions)
            ->field('attachment_id,
            pid,
            original_name,
			save_name,
			mime_type,
			description,
			size,
			attachment_type,
			external_id,
			external_id2,
			entered')
            ->order('attachment_id','desc')
            ->select();
        return $attaches;
    }
    public function deleteAttach($attachmentId){
        if ($attachmentId && !is_array($attachmentId)) {
            $attachmentId = explode(',',$attachmentId);
        }
        $where['attachment_id'] = count($attachmentId)>1 ? ['in',$attachmentId] : $attachmentId[0];
        Attachments::where($where)->setField('status',IndexDefs::ATTACHMENT_DEL);
        return true;
    }
    public function deleteAttaches($externalId, $attachmentType){
        Db::table('attachments')
            ->where(['attachment_type'=>$attachmentType,'external_id'=>$externalId,'status'=>IndexDefs::ATTACHMENT_OK])
            ->setField('status',IndexDefs::ATTACHMENT_DEL);
        return true;
    }
    /**
     * 关联附件
     * @param string|array $attachment_id
     * @param int $external_id
     * @param int $external_id2
     */
    public function relateAttaches($attachment_ids,$external_id,$external_id2=0){
        if (empty($attachment_ids)) {
            return;
        }
        if (!is_array($attachment_ids)) {
            $attachment_ids = explode(',', $attachment_ids);
        }
        $save['external_id'] = $external_id;
        if ($external_id2) {
            $save['external_id2'] = $external_id2;
        }
        Db::table('attachments')
            ->where(['attachment_id'=>['in',$attachment_ids]])
            ->update($save);
    }
}