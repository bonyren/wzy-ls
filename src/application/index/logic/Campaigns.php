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
use app\Defs;
use app\index\Defs as IndexDefs;
use think\Debug;
use think\Log;
use app\index\service\Setting as SettingService;
use app\index\logic\Mailboxes as MailboxesLogic;
use app\index\logic\Templates as TemplatesLogic;
use app\index\model\Campaigns as CampaignsModel;
use mail\MailMessage;
use hash\Uuid;
class Campaigns extends Base{
	public function loadDraftDatas(){
		$records = Db::table('campaigns')->where('status', IndexDefs::eCampaignStatusDraft)
			->field('campaign_id, subject, entered, unix_timestamp(now())-unix_timestamp(entered) as age')
			->select();
		foreach($records as &$record){
			$record['age'] = secs2time($record['age']);
		}
		return $records;
	}
	public function createCampaign(){
		$settingService = SettingService::I();
		$mailboxesLogic = MailboxesLogic::I();
		$templatesLogic = TemplatesLogic::I();
		//发送邮箱
		$mailboxId = 0;
		$defaultMailbox = $mailboxesLogic->getBlastMailbox();
		if($defaultMailbox){
			$mailboxId = $defaultMailbox['mailbox_id'];
		}
		//模板
		$templateId = 0;
		$defaultTemplate = $templatesLogic->getDefaultTemplate();
		if($defaultTemplate){
			$templateId = $defaultTemplate['template_id'];
		}
		//开始投递时间
		$defaultEmbargo = date('Y-m-d 12:00:00');
		//结束投递时间
		$defaultStopAfter = date('Y-m-d H:i:s', strtotime($defaultEmbargo . ' +1 week'));
		$datas = [
			'uuid'=>Uuid::generate(4),
			'mailbox_id'=>$mailboxId,
			'message_content'=>'',
			'template_id'=>$templateId,
			'send_format'=>IndexDefs::eCampaignSendFormatHtml,
			'embargo'=>$defaultEmbargo,
			'requeue_interval'=>IndexDefs::eRequeueIntervalNot,
			'stop_after'=>$defaultStopAfter,
			'status'=>IndexDefs::eCampaignStatusDraft,
		];
		$campaignId = Db::table('campaigns')->insertGetId($datas);
		return $campaignId;
	}
	public function deleteCampaign($campaignId){
		Db::table('campaign_attachments')->where('campaign_id', $campaignId)->delete();
		Db::table('campaign_datas')->where('campaign_id', $campaignId)->delete();
		Db::table('campaign_subscribers')->where('campaign_id', $campaignId)->delete();
		Db::table('campaigns_lists')->where('campaign_id', $campaignId)->delete();
		Db::table('link_track_campaign')->where('campaign_id', $campaignId)->delete();
		Db::table('link_track_subscriber')->where('campaign_id', $campaignId)->delete();
		Db::table('bounces_campaign_subscriber')->where('campaign_id', $campaignId)->delete();
		Db::table('campaigns')->where('campaign_id', $campaignId)->delete();
		return true;
	}
	public function getContent($campaignId){
		$contentInfos = Db::table('campaigns')->where('campaign_id', $campaignId)->field('mailbox_id,subject,message_content')->find();
		if(!$contentInfos){
			return false;
		}else{
			return $contentInfos;
		}
	}
	public function updateContent($campaignId, $mailboxId, $subject, $sendFormat, $templateId, $messageContent){
		Db::table('campaigns')->where('campaign_id', $campaignId)->update([
			'mailbox_id'=>$mailboxId,
			'subject'=>$subject,
			'send_format'=>$sendFormat,
			'template_id'=>$templateId,
			'message_content'=>$messageContent
		]);
		$this->revertCampaignDraft($campaignId);
		return true;
	}
	public function getFormat($campaignId){
		$formatInfos = Db::table('campaigns')->where('campaign_id', $campaignId)
			->field('template_id,send_format')->find();
		if(!$formatInfos){
			return false;
		}else{
			return $formatInfos;
		}
	}
	public function insertAttach($campaignId, $originalName, $saveName, $mimeType, $fileSize,$description){
		$campaignAttachId = Db::table('campaign_attachments')->insertGetId(
			[
				'campaign_id'=>$campaignId,
				'original_name'=>$originalName,
				'save_name'=>$saveName,
				'mime_type'=>$mimeType,
				'description'=>$description,
				'size'=>$fileSize
			]
		);
		$this->revertCampaignDraft($campaignId);
		return $campaignAttachId;
	}
	public function getAttaches($campaignId){
		$attaches = Db::table('campaign_attachments')->where('campaign_id', $campaignId)->field('campaign_attach_id,
			campaign_id,
			original_name,
			save_name,
			mime_type,
			description,
			size')->select();
		return $attaches;
	}
	public function deleteAttach($campaignAttachId){
		$campaignId = Db::table('campaign_attachments')->where('campaign_attach_id', $campaignAttachId)->value('campaign_id');
		Db::table('campaign_attachments')->where('campaign_attach_id', $campaignAttachId)->delete();
		$this->revertCampaignDraft($campaignId);
		return true;
	}

	public function updateScheduling($campaignId, $embargo, $stopAfter, $requeueInterval){
		Db::table('campaigns')->where('campaign_id', $campaignId)->update([
			'embargo'=>$embargo,
			'stop_after'=>$stopAfter,
			'requeue_interval'=>$requeueInterval
		]);
		$this->revertCampaignDraft($campaignId);
		return true;
	}
	public function getScheduling($campaignId){
		$schedulingInfos = Db::table('campaigns')->where('campaign_id', $campaignId)->field('embargo,stop_after,requeue_interval')->find();
		if(!$schedulingInfos){
			return false;
		}else{
			return $schedulingInfos;
		}
	}
	public function updateLists($campaignId, $lists){
		//////////////////////////////////////////////////////////////////////
		$listId = (int)$lists[0];
		if($listId == 0){
			//all lists
			$lists = Db::table('lists')->column('list_id');
		}else if($listId == -1){
			//all public lists
			$lists = Db::table('lists')->where('public', IndexDefs::eListPublic)->column('list_id');
		}
		//1.update the campaigns_lists
		//1.1 clean obsolete
		$listsInDb = Db::table('campaigns_lists')->where('campaign_id', $campaignId)->column('list_id');
		foreach ($listsInDb as $listId) {
			if(!in_array($listId, $lists)){
				Db::table('campaigns_lists')->where(['campaign_id'=>$campaignId, 'list_id'=>$listId])->delete();
			}
		}
		//1.2 insert new
		foreach ($lists as $listId) {
			$existCount = Db::table('campaigns_lists')->where(['campaign_id'=>$campaignId, 'list_id'=>$listId])->count();
			if($existCount == 0){
				Db::table('campaigns_lists')->insert([
					'campaign_id'=>$campaignId,
					'list_id'=>$listId,
					'entered'=>date('Y-m-d H:i:s')
				]);
			}
		}
		//2.update the campaign_subscribers, the following code has memory risk
		/*ProcessQueue will do it
		//2.1 get all subscribers from db according to $lists
		$allSubscribersIds = [];
		foreach ($lists as $listId) {
			$subscriberIds = Db::table('list_subscribers')->where('list_id', $listId)->column('subscriber_id');
			$allSubscribersIds = array_merge($allSubscribersIds, $subscriberIds);
		}
		//2.2 clean obsolete
		$subscribersInDb = Db::table('campaign_subscribers')->where('campaign_id', $campaignId)->column('subscriber_id');
		foreach ($subscribersInDb as $subscriberId) {
			if(!in_array($subscriberId, $allSubscribersIds)){
				Db::table('campaign_subscribers')->where(['campaign_id'=>$campaignId, 'subscriber_id'=>$subscriberId])->delete();
			}
		}
		//2.3 insert new
		foreach ($allSubscribersIds as $subscriberId) {
			$existCount = Db::table('campaign_subscribers')->where(['campaign_id'=>$campaignId, 'subscriber_id'=>$subscriberId])->count();
			if($existCount == 0){
				Db::table('campaign_subscribers')->insert([
					'campaign_id'=>$campaignId,
					'subscriber_id'=>$subscriberId,
					'entered'=>date('Y-m-d H:i:s')
				]);
			}
		}*/
		$this->revertCampaignDraft($campaignId);
		return true;
	}
	////////////////////////////////////////////////////////////////////////////
	public function refreshAllCampaignUuid(){
		$campaignModels =CampaignsModel::all(['uuid'=>'']);
		foreach($campaignModels as $campaignModel){
			$campaignModel->save(['uuid'=>Uuid::generate(4)]);
		}
	}
	public function addCampaignProcessQueue($campaignId){
		Db::table('campaigns')->where('campaign_id', $campaignId)
			->update(['status'=>IndexDefs::eCampaignStatusSubmitted]);
		return true;
	}
	public function revertCampaignDraft($campaignId){
		Db::table('campaigns')->where('campaign_id', $campaignId)
			->update(['status'=>IndexDefs::eCampaignStatusDraft]);
		return true;
	}
	public function verifyCampaignData($campaignId){
		$campaignData = Db::table('campaigns')->where('campaign_id', $campaignId)->find();
		if(!$campaignData){
			exception("无法找到该投递活动");
		}
		//主题为空
		if(empty(trim($campaignData['subject']))){
			exception("主题为空");
		}
		//内容为空
		if(empty(trim($campaignData['message_content']))){
			exception("内容为空");
		}
		//没有配置发送邮箱
		if(empty(trim($campaignData['mailbox_id']))){
			exception("没有配置发送邮箱");
		}
		//投递目标订阅者集合为空
		$listIds = Db::table('campaigns_lists')->where(['campaign_id'=>$campaignId])->column('list_id');
		if(empty($listIds)){
			exception("投递目标订阅者集合为空");
		}
	}
	////////////////////////////////////////////////////////////////////////////
	public function loadCampaignData($campaignId){
		$campaignData = [
			'campaign_id'=>0,
			'uuid'=>'',
			'mailbox_id'=>0,
			'subject'=>'',

			'message_content'=>'',
			'message_footer'=>'',

			'template_id'=>0,
			'send_format'=>IndexDefs::eCampaignSendFormatHtml,

			'embargo'=>Defs::DEFAULT_DB_DATETIME_VALUE,

			'requeue_interval'=>IndexDefs::eRequeueIntervalNot,

			'stop_after'=>Defs::DEFAULT_DB_DATETIME_VALUE,

			'status'=>IndexDefs::eCampaignStatusDraft,
			'target_lists'=>[],

			'entered'=>'',

			'send_start_time'=>Defs::DEFAULT_DB_DATETIME_VALUE,
			'send_end_time'=>Defs::DEFAULT_DB_DATETIME_VALUE,

			'viewed_count'=>0,
			'send_text_count'=>0,
			'send_html_count'=>0,
			'processed_count'=>0,
			'bounce_count'=>0,

			'send_start_alert'=>systemSetting('CAMPAIGN_NOTIFYSTART'),
			'send_end_alert'=>systemSetting('CAMPAIGN_NOTIFYEND')
		];
		$campaignDbData = Db::table('campaigns')->where('campaign_id', $campaignId)->field('*')->find();
		if(!$campaignDbData){
			return [];
		}
		$campaignData['campaign_id'] = $campaignDbData['campaign_id'];
		$campaignData['uuid'] = $campaignDbData['uuid'];
		$campaignData['mailbox_id'] = $campaignDbData['mailbox_id'];
		$campaignData['subject'] = htmlspecialchars_decode($campaignDbData['subject']);

		$campaignData['message_content'] = htmlspecialchars_decode($campaignDbData['message_content']);
		$campaignData['message_footer'] = systemSetting('CAMPAIGN_MESSAGEFOOTER');

		$campaignData['template_id'] = $campaignDbData['template_id'];
		$campaignData['send_format'] = $campaignDbData['send_format'];

		$campaignData['requeue_interval'] = $campaignDbData['requeue_interval'];
		$campaignData['stop_after'] = $campaignDbData['stop_after'];

		$campaignData['entered'] = $campaignDbData['entered'];
		$campaignData['status'] = $campaignDbData['status'];
		$targetLists = Db::table('campaigns_lists')->where('campaign_id', $campaignId)->column('list_id');
		$campaignData['target_lists'] = $targetLists;

		$campaignData['send_start_time'] = $campaignDbData['send_start_time'];
		$campaignData['send_end_time'] = $campaignDbData['send_end_time'];

		$campaignData['viewed_count'] = $campaignDbData['viewed_count'];
		$campaignData['send_text_count'] = $campaignDbData['send_text_count'];
		$campaignData['send_html_count'] = $campaignDbData['send_html_count'];
		$campaignData['processed_count'] = $campaignDbData['processed_count'];
		$campaignData['bounce_count'] = $campaignDbData['bounce_count'];

		return $campaignData;
	}
	public function setCampaignData($campaignId, $name, $data){
		$existCount = Db::table('campaign_datas')->where(['campaign_id'=>$campaignId, 'name'=>$name])->count();
		if($existCount > 0){
			Db::table('campaign_datas')->where(['campaign_id'=>$campaignId, 'name'=>$name])->update([
				'data'=>$data
			]);
		}else{
			Db::table('campaign_datas')->insert([
				'campaign_id'=>$campaignId,
				'name'=>$name,
				'data'=>$data
			]);
		}
		return true;
	}
	public static $campaignCaches = [];
	public function preCacheCampaign($campaignId){
		$campaignData = $this->loadCampaignData($campaignId);
		if(!$campaignData){
			Log::error("failed to load campaign data, campaign id: $campaignId");
			return false;
		}
		////////////////////////////////////////////////////////////
		$campaignCache = [];
		foreach($campaignData as $key=>$value){
			$campaignCache[$key] = $value;
		}
		////////////////////////////////////////////////////////////
		if (strip_tags($campaignCache['message_content']) != $campaignCache['message_content']) {
			//html footer
			$campaignCache['text_message'] = MailMessage::html2text($campaignCache['message_content']);
			$campaignCache['html_message'] = $campaignCache['message_content'];
		} else {
			//text footer
			$campaignCache['text_message'] = $campaignCache['message_content'];
			$campaignCache['html_message'] = MailMessage::text2html($campaignCache['message_content']);
		}
		////////////////////////////////////////////////////////////
		if (strip_tags($campaignCache['message_footer']) != $campaignCache['message_footer']) {
			//html footer
			$campaignCache['text_footer'] = MailMessage::html2text($campaignCache['message_footer']);
			$campaignCache['html_footer'] = $campaignCache['message_footer'];
		} else {
			//text footer
			$campaignCache['text_footer'] = $campaignCache['message_footer'];
			$campaignCache['html_footer'] = MailMessage::text2html($campaignCache['message_footer']);
		}
		//true or false
		$campaignCache['html_formatted'] = strip_tags($campaignCache['message_content']) != $campaignCache['message_content'];

		if ($campaignData['template_id']) {
			$template = Db::table('templates')->where('template_id', $campaignData['template_id'])->value('template');
			if($template === null){
				$template = '';
			}
			$campaignCache['template'] = htmlspecialchars_decode($template);
		} else {
			$campaignCache['template'] = '';
		}
		////////////////////////////////////////////////////////////
		$campaignCache['html_charset'] = 'UTF-8';
		$campaignCache['text_charset'] = 'UTF-8';

		foreach (array('subject', 'campaign_id') as $key) {
			$val = $campaignData[$key];
			if (!is_array($val)) {
				$campaignCache['html_message'] = str_ireplace("[$key]", $val, $campaignCache['html_message']);
				$campaignCache['text_message'] = str_ireplace("[$key]", $val, $campaignCache['text_message']);
				$campaignCache['text_footer'] = str_ireplace("[$key]", $val, $campaignCache['text_footer']);
				$campaignCache['html_footer'] = str_ireplace("[$key]", $val, $campaignCache['html_footer']);
			}
		}
		self::$campaignCaches[$campaignId] = $campaignCache;
		return true;
	}
}