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
use app\index\Defs as IndexDefs;
use think\Db;
use think\Log;
use think\Debug;
use app\index\model\Subscribers as SubscribersModel;
use app\index\logic\Progress as ProgressLogic;

class Subscribers extends Base{
	public function __construct(){
		parent::__construct();
	}
	public function load($listId, 
		$search=array(), 
		$page=1, 
		$rows=DEFAULT_PAGE_ROWS,
		$sort = '',
		$order = ''){
		/////////////////////////////////////////////////
        if($sort == 'email'){
        	$order = 'S.email ' . $order;
        }else if($sort == 'modified'){
			$order = 'S.modified ' . $order;
		}else{
			$order = 'S.subscriber_id desc';
		}
        /////////////////////////////////////////////////
        $conditions = ['S.deleted'=>0];
        if($listId == 0){
			//全部
        }else if($listId == -1){
			//未归集
			$conditions['LS.list_id'] = null;
		}else{
			$conditions['LS.list_id'] = $listId;
		}
        if(!emptyInArray($search, 'email')){
        	$conditions['S.email'] = ['like', "%{$search['email']}%"];
        }
		//以下为boolean搜索
		if(!emptyStringInArray($search, 'blacklisted')){
			$conditions['S.blacklisted'] = (int)$search['blacklisted'];
		}
        //////////////////////////////////////////////////
		$totalCount = Db::table('subscribers')->alias('S')
			->join('list_subscribers LS', 'LS.subscriber_id=S.subscriber_id', 'LEFT')
			->where($conditions)
			->count();
        $subscribers = Db::table('subscribers')->alias('S')
			->join('list_subscribers LS', 'LS.subscriber_id=S.subscriber_id', 'LEFT')
			->where($conditions)
			->page($page, $rows)
			->order($order)
			->field("S.*")
			->select();
		for($i=0,$count=count($subscribers); $i<$count; $i++){
			$subscriberId = $subscribers[$i]['subscriber_id'];
			//参与投递活动次数
			$campaignIds = Db::table("campaigns_lists")->alias('CL')->distinct(true)
				->join('list_subscribers LS', 'CL.list_id=LS.list_id')
				->where('LS.subscriber_id', $subscriberId)
				->column('campaign_id');
			$subscribers[$i]['campaigns_count'] = count($campaignIds);
		}
		return [
			'total'=>$totalCount,
			'rows'=>$subscribers
		];
	}
	public function loadSearch($search=array(), 
		$page=1, 
		$rows=DEFAULT_PAGE_ROWS,
		$sort = '',
		$order = ''){
		//排序
		if($sort == 'email'){
			$order = 'S.email ' . $order;
		}else if($sort == 'modified'){
			$order = 'S.modified ' . $order;
		}else{
			$order = 'S.subscriber_id desc';
		}
        /////////////////////////////////////////////////
        $conditions = ['S.deleted'=>0];
        if(isset($search['email']) && !empty($search['email'])){
        	$conditions['S.email'] = ['like', "%{$search['email']}%"];
        }
		//boolean搜索
		if(!emptyStringInArray($search, 'blacklisted')){
        	$conditions['S.blacklisted'] = (int)$search['blacklisted'];
        }
		//所属集合
		if(isset($search['list']) && $search['list'] !== '0'){
			if($search['list']) {
				$conditions['LS.list_id'] = $search['list'];
			}else{
				//不在任何集合的订阅者
				$conditions['LS.list_id'] = null;
			}
		}
        //////////////////////////////////////////////////
		$totalCount = Db::table('subscribers')->alias('S')
			->join('list_subscribers LS', 'LS.subscriber_id=S.subscriber_id', 'LEFT')
			->group('S.subscriber_id')
			->where($conditions)
			->count();
        $subscribers = Db::table('subscribers')->alias('S')
			->join('list_subscribers LS', 'LS.subscriber_id=S.subscriber_id', 'LEFT')
			->group('S.subscriber_id')
			->where($conditions)
			->page($page, $rows)
			->order($order)
			->field("S.*")
			->select();
		for($i=0,$count=count($subscribers); $i<$count; $i++){
			$subscriberId = $subscribers[$i]['subscriber_id'];
			//所在的集合
			$subscribers[$i]['lists_count'] = Db::table("list_subscribers")->where('subscriber_id', $subscriberId)->count();
			//参与的投递活动
			$campaignIds = Db::table("campaigns_lists")->alias('CL')->distinct(true)
				->join('list_subscribers LS', 'CL.list_id=LS.list_id')
				->where('LS.subscriber_id', $subscriberId)
				->column('campaign_id');
			$subscribers[$i]['campaigns_count'] = count($campaignIds);
		}
		return [
			'total'=>$totalCount,
			'rows'=>$subscribers
		];
	}
	public function addSubscriber($email, $name, $details, $lists){
		//Log::debug('addSubscriber input param: ' . var_export(func_get_args(), true));
		//////////////////////////////////////////////////////
		$insertDatas = array(
			'uniqid'=>generateUniqid(),
			'uuid'=>\hash\Uuid::generate(4),
			'email'=>$email,
			'name'=>$name,
			'blacklisted'=>0,
			'html_email'=>isset($details['html_email'])?IndexDefs::eSubscriberHtmlEmail:IndexDefs::eSubscriberTextEmail,
			'deleted'=>0,
			'bounce_count'=>0);

		Db::table('subscribers')->insert($insertDatas);
		$subscriberId = Db::table('subscribers')->getLastInsID();
		//////////////////////////////////////////////////////
		if(empty($lists)){
			return true;
		}
		foreach($lists as $key=>$listId) {
			Db::table('list_subscribers')->insert([
				'subscriber_id' => $subscriberId,
				'list_id' => $listId,
				'entered' => date('Y-m-d H:i:s')
			]);
		}
		return true;
	}
	public function editSubscriber($subscriberId, $email, $name, $details, $lists){
		//Log::debug('editSubscriber input param: ' . var_export(func_get_args(), true));
		$updateDatas = array('email'=>$email,
			'name'=>$name,
			'html_email'=>isset($details['html_email'])?IndexDefs::eSubscriberHtmlEmail:IndexDefs::eSubscriberTextEmail
		);
		Db::table('subscribers')->where(['subscriber_id'=>$subscriberId])->update($updateDatas);
		//////////////////////////////////////////////////////
		//insert
		foreach($lists as $listId) {
			$count = Db::table('list_subscribers')->where(['subscriber_id' => $subscriberId, 'list_id' => $listId])->count();
			if($count == 0){
				//Log::notice("insert into list_subscribers, subscriber_id:{$subscriberId}, list_id:{$listId}");
				Db::table('list_subscribers')->insert([
					'subscriber_id' => $subscriberId,
					'list_id' => $listId
				]);
			}
		}
		//remove
		$existListIds = Db::table('list_subscribers')->where(['subscriber_id' => $subscriberId])->column('list_id');
		foreach ($existListIds as $listId) {
			if(!in_array($listId, $lists)){
				//Log::notice("delete from list_subscribers, subscriber_id:{$subscriberId}, list_id:{$listId}");
				Db::table('list_subscribers')->where(['subscriber_id' => $subscriberId, 'list_id' => $listId])->delete();
			}
		}
		return true;
	}
	public function removeSubscriber($subscriberId, $listId){
		Db::table('list_subscribers')
			->where(['subscriber_id'=>$subscriberId, 'list_id'=>$listId])
			->delete();
		return true;
	}
	public function deleteSubscriber($subscriberId){
		//Db::table('subscribers')->where(['subscriber_id'=>$subscriberId])->delete();
		Db::table('subscribers')->where(['subscriber_id'=>$subscriberId])->update(['deleted'=>1]);
		//Db::table('list_subscribers')->where(['subscriber_id'=>$subscriberId])->delete();
		//Db::table('campaign_subscribers')->where(['subscriber_id'=>$subscriberId])->delete();
		//Db::table('link_track_subscriber')->where(['subscriber_id'=>$subscriberId])->delete();
		//Db::table('bounces_campaign_subscriber')->where(['subscriber_id'=>$subscriberId])->delete();
		return true;
	}
	public function whitelistSubscriber($subscriberId){
		Db::table('subscribers')->where(['subscriber_id'=>$subscriberId])
			->update(['blacklisted'=>0]);
		return true;
	}
	public function blacklistSubscriber($subscriberId){
		Db::table('subscribers')->where(['subscriber_id'=>$subscriberId])
			->update(['blacklisted'=>1]);
		return true;
	}
	public function getSubscriber($subscriberId){
		$subscriber = Db::table('subscribers')->where('subscriber_id', $subscriberId)->field('*')->find();
		return $subscriber;
	}
	public function getSubscriberByEmail($email){
		$subscriber = Db::table('subscribers')->where('email', $email)->field('*')->find();
		return $subscriber;
	}
	public function getSubscriberByUniqid($uid){
		$subscriber = Db::table('subscribers')->where('uniqid', $uid)->field('*')->find();
		return $subscriber;
	}
	public function getSubscriberLists($subscriberId){
		return Db::table('list_subscribers')->where('subscriber_id', $subscriberId)->column('list_id');
	}
	public function getSubscriberListNames($subscriberId){
		$listNames = Db::table('list_subscribers')->alias('LS')->join('lists L', 'LS.list_id=L.list_id')->where('LS.subscriber_id', $subscriberId)->column('L.name');
		return $listNames;
	}
	public function getSubscriberListIds($subscriberId){
		$listIds = Db::table('list_subscribers')->alias('LS')->join('lists L', 'LS.list_id=L.list_id')->where('LS.subscriber_id', $subscriberId)->column('L.list_id');
		return $listIds;
	}
	public function saveSubscriberList($subscriberId, $listId){
		$record = Db::table('list_subscribers')->where(['subscriber_id'=>$subscriberId, 'list_id'=>$listId])->find();
		if(!$record){
			Db::table('list_subscribers')->insert(['subscriber_id'=>$subscriberId, 'list_id'=>$listId]);
		}
	}
	/******************************************************************************************************************/
	public function downloadSubscriber($dateType, $fields, $dateFrom = null, $dateTo = null, $listId = null){
		set_time_limit(0);
		$fileName = "camellist-subscribers-" . date('Y-m-d-H-i-s');
		header('Content-Type: application/download');
		header('Content-Type: text/csv;charset=UTF-8');
		header('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
		header('Cache-Control: max-age=0');
		$fp = fopen('php://output', 'a');
		//BOM header
		//fwrite($fp, chr(0XEF) . chr(0xBB) . chr(0XBF));
		$heads = array();
		$fieldNames = array('subscriber_id');
		if(in_array(IndexDefs::eSubscriberExportFieldId, $fields)){
			$heads[] = 'uniqid';
			$fieldNames[] = 'uniqid';
		}
		if(in_array(IndexDefs::eSubscriberExportFieldEmail, $fields)){
			$heads[] = 'email';
			$fieldNames[] = 'email';
		}
		if(in_array(IndexDefs::eSubscriberExportFieldBlacklisted, $fields)){
			$heads[] = 'blacklisted';
			$fieldNames[] = 'blacklisted';
		}
		if(in_array(IndexDefs::eSubscriberExportFieldEntered, $fields)){
			$heads[] = 'entered';
			$fieldNames[] = 'entered';
		}
		if(in_array(IndexDefs::eSubscriberExportFieldLastModified, $fields)){
			$heads[] = 'modified';
			$fieldNames[] = 'modified';
		}
		if(in_array(IndexDefs::eSubscriberExportFieldBounceCount, $fields)){
			$heads[] = 'bounce_count';
			$fieldNames[] = 'bounce_count';
		}
		fputcsv($fp, $heads);
		//$subscribers = [];
		//大数据集合导出，要考虑内存消耗
		if(IndexDefs::eSubscriberExportDateAny == $dateType){
			if($listId){
				$fieldNames = array_map(function($item){
					return 'S.' . $item;
				}, $fieldNames);

				Db::table('subscribers')->alias('S')->join('list_subscribers LS', 'LS.subscriber_id=S.subscriber_id')
					->where(['LS.list_id'=>$listId, 'S.deleted'=>0])->field($fieldNames)->chunk(100, function ($subscribers) use ($fp) {
						foreach ($subscribers as $key => &$subscriber) {
							unset($subscriber['subscriber_id']);
							fputcsv($fp, array_values($subscriber));
						}
					}, 'S.subscriber_id');
			}else {
				Db::table('subscribers')->field(implode(',', $fieldNames))->chunk(100, function ($subscribers) use ($fp) {
					foreach ($subscribers as $key => &$subscriber) {
						unset($subscriber['subscriber_id']);
						fputcsv($fp, array_values($subscriber));
					}
				});
			}
		}else if(IndexDefs::eSubscriberExportDateChanged == $dateType){
			if($listId){
				$fieldNames = array_map(function($item){
					return 'S.' . $item;
				}, $fieldNames);
				Db::table('subscribers')->alias('S')->join('list_subscribers LS', 'LS.subscriber_id=S.subscriber_id')
					->where(['LS.list_id'=>$listId, 'S.deleted'=>0])->whereTime('S.modified', 'between', [$dateFrom, $dateTo])->field($fieldNames)
					->chunk(100, function ($subscribers) use ($fp) {
						foreach ($subscribers as $key => &$subscriber) {
							unset($subscriber['subscriber_id']);
							fputcsv($fp, array_values($subscriber));
						}
					}, 'S.subscriber_id');
			}else {
				Db::table('subscribers')->whereTime('modified', 'between', [$dateFrom, $dateTo])->field(implode(',', $fieldNames))
					->chunk(100, function ($subscribers) use ($fp) {
						foreach ($subscribers as $key => &$subscriber) {
							unset($subscriber['subscriber_id']);
							fputcsv($fp, array_values($subscriber));
						}
					});
			}
		}
		ob_flush();
		flush();
		fclose($fp);
	}
	protected function importSubscribers($lists, $emails){
		//update and insert subscribers
		$subscriberIds = [];
		$emailNewCount = 0;
		$emailExistCount = 0;
		foreach($emails as $key=>$email){
			$email = trim($email);
			if(!validateEmail($email)){
				Log::error("importSubscribers - the email $email format is wrong");
				continue;
			}
			$existCount = Db::table('subscribers')->where('email', $email)->count();
			if($existCount == 0) {
				++$emailNewCount;
				$subscriberId = Db::table('subscribers')->insertGetId(['email' => $email,
					'uniqid'=>generateUniqid(),
					'uuid'=>\hash\Uuid::generate(4),
					'blacklisted'=>0,
					'entered' => date('Y-m-d H:i:s'),
					'bounce_count'=>0]);
			}else{
				++$emailExistCount;
				$subscriberId = Db::table('subscribers')->where('email', $email)->value('subscriber_id');
			}
			$subscriberIds[] = $subscriberId;
		}
		//update and insert list_subscribers
		$emailAddToListCount = 0;
		foreach($lists as $listKey=>$listId){
			if($listId == -1){
				//未归集
				continue;
			}
			foreach($subscriberIds as $subscriberKey=>$subscriberId){
				$existCount = Db::table('list_subscribers')->where(['subscriber_id'=>$subscriberId, 'list_id'=>$listId])->count();
				if($existCount == 0) {
					++$emailAddToListCount;
					Db::table('list_subscribers')->insert(
						[
							'subscriber_id' => $subscriberId,
							'list_id' => $listId,
							'entered' => date('Y-m-d H:i:s')
						]
					);
				}
			}
		}
		//update the result
		$importResults = [
			'email_add_to_list'=>$emailAddToListCount,
			'email_new'=>$emailNewCount,
			'email_exist'=>$emailExistCount
		];
		return $importResults;
	}
	public function importSimple($lists, $emailListsStr, &$importResults){
		Log::info('importSimple, email list: ' . $emailListsStr);
		$emails = explode("\n", $emailListsStr);
		if(empty($emails)){
			Log::error("importSimple, the emails count is 0");
			return true;
		}
		Log::notice("importSimple, email count: " . count($emails));
		$importResults['lines_processed'] = count($emails);
		///////////////////////////////////////////////////
		if(empty($lists)){
			Log::error("importSimple the lists count is 0");
			return true;
		}
		foreach($lists as $key=>$listId) {
			$otherLists = [];
			if ($listId == IndexDefs::eSubscriberImportAllLists) {
				//all lists
				$otherLists = Db::table('lists')->column('list_id');
				unset($lists[$key]);
			}
			if($otherLists){
				foreach($otherLists as $otherListId){
					if(!in_array($otherListId, $lists)){
						$lists[] = $otherListId;
					}
				}
			}
		}
		Log::notice('import lists: ' . implode(',', $lists));
		$result = $this->importSubscribers($lists, $emails);
		$importResults['email_add_to_list'] = $result['email_add_to_list'];
		$importResults['email_new'] = $result['email_new'];
		$importResults['email_exist'] = $result['email_exist'];
		return true;
	}
	public function importFile($lists, $filePath, &$importResults){
		$f = fopen($filePath, 'r');
		if(!$f){
			return false;
		}
		ProgressLogic::I()->register(ProgressLogic::PROGRESS_SUBSCRIBERS_IMPORT_FILE_TYPE);

		$emails = array();
		while(($line = fgets($f)) !== false){
			$pos = strpos($line, ' ');
			if($pos !== false){
				$line = substr($line, 0, $pos);
			}
			$line = trim($line);
			if($line && validateEmail($line)) {
				$emails[] = $line;
			}else{
				Log::error("skip the line from import file, " . $line);
			}
		}
		fclose($f);
		ProgressLogic::I()->updateProgress(ProgressLogic::PROGRESS_SUBSCRIBERS_IMPORT_FILE_TYPE,
			sprintf("当前 %u / 总共 %u", 0, count($emails)));

		$importResults['lines_processed'] = count($emails);
		///////////////////////////////////////////////////
		if(empty($lists)){
			Log::error("importFile the lists count is 0");
			return true;
		}
		foreach($lists as $key=>$listId) {
			$otherLists = [];
			if ($listId == IndexDefs::eSubscriberImportAllLists) {
				//all lists
				$otherLists = Db::table('lists')->column('list_id');
				unset($lists[$key]);
			}
			if($otherLists){
				foreach($otherLists as $otherListId){
					if(!in_array($otherListId, $lists)){
						$lists[] = $otherListId;
					}
				}
			}
		}
		Log::notice('import lists: ' . implode(',', $lists));
		$result = $this->importSubscribers($lists, $emails);
		$importResults['email_add_to_list'] = $result['email_add_to_list'];
		$importResults['email_new'] = $result['email_new'];
		$importResults['email_exist'] = $result['email_exist'];
		ProgressLogic::I()->unregister(ProgressLogic::PROGRESS_SUBSCRIBERS_IMPORT_FILE_TYPE);
		return true;
	}
	public function importFileTest($filePath, &$emails){
		$f = fopen($filePath, 'r');
		if(!$f){
			return false;
		}
		while(($line = fgets($f)) !== false){
			$pos = strpos($line, ' ');
			if($pos !== false){
				$line = substr($line, 0, $pos);
			}
			$line = trim($line);
			if($line && validateEmail($line)) {
				$emails[] = $line;
			}else{
				Log::error("skip the line from import file, " . $line);
			}
			if(count($emails) >= 50){
				break;
			}
		}
		fclose($f);
		return true;
	}
	public function importCsvFile($lists, $filePath, $isOverwrite, &$importResults){
		$f = fopen($filePath, 'r');
		if(!$f){
			Log::error("failed to open the file $filePath");
			return false;
		}
		$emails = array();
		//read from file to memory
		$fields = null;
		$items = [];
		$index = 0;
		while(($data = fgetcsv($f)) !== false){
			if($index == 0){
				$fields = $data;
			}else{
				$items[] = $data;
			}
			$index++;
		}
		fclose($f);
		if(!$fields){
			Log::error("failed to find the fields, path: $filePath");
			return false;
		}
		//check if all fields are valid
		$invalid = false;
		$validFields = ['uniqid', 'email', 'blacklisted','entered', 'modified', 'bounce_count'];
		foreach($fields as $field){
			if(!in_array(trim($field), $validFields)){
				$invalid = true;
				break;
			}
		}
		if($invalid){
			Log::error("failed to valid the input fields");
			return false;
		}
		//sanitize the data
		$emailIndex = -1;
		for($i=0,$count=count($fields); $i<$count; $i++){
			$fields[$i] = trim($fields[$i]);
			if(strtolower($fields[$i]) == 'email'){
				$emailIndex = $i;
			}
		}
		if($emailIndex == -1){
			Log::error("failed to find the email field in csv first line, fields: " . var_export($fields, true));
			return false;
		}
		//get the importing lists
		///////////////////////////////////////////////////
		if(empty($lists)){
			Log::error("importCsvFile the lists count is 0");
			return true;
		}
		foreach($lists as $key=>$listId) {
			$otherLists = [];
			if ($listId == IndexDefs::eSubscriberImportAllLists) {
				//all lists
				$otherLists = Db::table('lists')->column('list_id');
				unset($lists[$key]);
			}
			if($otherLists){
				foreach($otherLists as $otherListId){
					if(!in_array($otherListId, $lists)){
						$lists[] = $otherListId;
					}
				}
			}
		}
		Log::notice('import lists: ' . implode(',', $lists));
		//update and insert subscribers
		$subscriberIds = [];
		$emailNewCount = 0;
		$emailExistCount = 0;
		foreach($items as $item){
			if(count($item) != count($fields)){
				Log::error("skip the import line, " . var_export($item, true));
				continue;
			}
			$email = trim($item[$emailIndex]);
			if(!validateEmail($email)){
				Log::error("skip the import line, can't find the email field. " . var_export($item, true));
				continue;
			}
			$existCount = Db::table('subscribers')->where('email', $email)->count();
			if($existCount == 0) {
				++$emailNewCount;
				//insert
				$insertDatas = [];
				for($i=0,$count=count($fields); $i<$count; $i++){
					$insertDatas[$fields[$i]] = $item[$i];
				}
				$subscriberId = Db::table('subscribers')->insertGetId($insertDatas);
			}else{
				++$emailExistCount;
				//update
				if($isOverwrite) {
					$updateDatas = [];
					for ($i = 0, $count = count($fields); $i < $count; $i++) {
						if ($i == $emailIndex) {
							continue;
						}
						$updateDatas[$fields[$i]] = $item[$i];
					}
					Db::table('subscribers')->where('email', $email)->update($updateDatas);
				}
				$subscriberId = Db::table('subscribers')->where('email', $email)->value('subscriber_id');
			}
			$subscriberIds[] = $subscriberId;
		}
		//update and insert list_subscribers
		$emailAddToListCount = 0;
		foreach($lists as $listKey=>$listId){
			foreach($subscriberIds as $subscriberKey=>$subscriberId){
				$existCount = Db::table('list_subscribers')->where(['subscriber_id'=>$subscriberId, 'list_id'=>$listId])->count();
				if($existCount == 0) {
					++$emailAddToListCount;
					Db::table('list_subscribers')->insert(
						[
							'subscriber_id' => $subscriberId,
							'list_id' => $listId,
							'entered' => date('Y-m-d H:i:s')
						]
					);
				}
			}
		}
		//update the result
		$importResults['lines_processed'] = count($items);
		$importResults['email_add_to_list'] = $emailAddToListCount;
		$importResults['email_new'] = $emailNewCount;
		$importResults['email_exist'] = $emailExistCount;
		return true;
	}
	public function importCsvFileTest($filePath, &$emails){
		$f = fopen($filePath, 'r');
		if(!$f){
			return false;
		}
		$emails = array();
		//read from file to memory
		$fields = null;
		$items = [];
		$index = 0;
		while(($data = fgetcsv($f)) !== false){
			if($index == 0){
				Log::notice("importCsvFileTest, fields data: " . var_export($data, true));
				$fields = $data;
			}else{
				$items[] = $data;
			}
			if(count($items) > 50){
				break;
			}
			$index++;
		}
		fclose($f);
		if(!$fields){
			return false;
		}
		//check if all fields are valid
		$invalid = false;
		$validFields = ['uniqid', 'email', 'blacklisted','entered', 'modified', 'bounce_count'];
		foreach($fields as $field){
			if(!in_array(trim($field), $validFields)){
				$invalid = true;
				break;
			}
		}
		if($invalid){
			Log::error("failed to valid the input fields");
			return false;
		}
		Log::notice("importCsvFileTest, fields count: " . count($fields));
		Log::notice("importCsvFileTest, fields: " . var_export($fields, true));
		Log::notice("importCsvFileTest, item count: " . count($items));
		//sanitize the data
		$emailIndex = -1;
		for($i=0,$count=count($fields); $i<$count; $i++){
			$fields[$i] = trim($fields[$i]);
			if(strtolower($fields[$i]) == 'email'){
				$emailIndex = $i;
			}
		}
		if($emailIndex == -1) {
			Log::error("failed to find the email field in csv first line, fields: " . var_export($fields, true));
			return false;
		}
		foreach($items as $item) {
			if (count($item) != count($fields)) {
				Log::error("skip the import line, " . var_export($item, true));
				continue;
			}
			$email = $item[$emailIndex];
			$emails[] = $email;
		}
		return true;
	}

	/**
	 * @param $email
	 * @throws \hash\Exception
	 */
	public function refreshSubscriberUuid($email){
		$subscribersModel = model('app\index\model\Subscribers');
		$uuid = $subscribersModel->where('email', $email)->value('uuid');
		if(empty($uuid)) {
			$subscribersModel->save(['uuid'=>\hash\Uuid::generate(4)], ['email'=>$email]);
		}
		$uniqid = $subscribersModel->where('email', $email)->value('uniqid');
		if(empty($uniqid)){
			$subscribersModel->save(['uniqid'=>generateUniqid()], ['email'=>$email]);
		}
	}
	public function refreshAllSubscriberUuid(){
		$subscriberModels =SubscribersModel::all(['uuid'=>'']);
		foreach($subscriberModels as $subscriberModel){
			$subscriberModel->save(['uuid'=>\hash\Uuid::generate(4)]);
		}
	}
	public function refreshAllSubscriberUniqid(){
		$subscriberModels =SubscribersModel::all(['uniqid'=>'']);
		foreach($subscriberModels as $subscriberModel){
			$subscriberModel->save(['uniqid'=>generateUniqid()]);
		}
	}
}

?>