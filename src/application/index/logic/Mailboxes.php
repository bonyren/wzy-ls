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
use think\Debug;
use app\index\model\Mailboxes as MailboxesModel;

class Mailboxes extends Base{
    public function __construct(){
        parent::__construct();
    }
    public function load($search=array(),
                         $page=1,
                         $rows=DEFAULT_PAGE_ROWS,
                         $sort = '',
                         $order = ''){
        $order = 'mailbox_id desc';
        $conditions = [];
        $totalCount = Db::table('mailboxes')->where($conditions)->count();
        $records = Db::table('mailboxes')
            ->where($conditions)
            ->page($page, $rows)
            ->order($order)
            ->field(true)
            ->select();
        return [
            'total'=>$totalCount,
            'rows'=>$records
        ];
    }
    public function loadComboboxDatas(){
        $records = Db::table('mailboxes')->field('mailbox_id, title')->select();
        array_unshift($records, ['mailbox_id'=>0, 'title'=>'All']);
        return $records;
    }
    public function addMailbox($infos){
        $mailboxModel = new MailboxesModel();
        if($this->getSystemMailbox()){
            $infos['system'] = MailboxesModel::eMailboxSystemNo;
        }else{
            $infos['system'] = MailboxesModel::eMailboxSystemYes;
        }
        if($this->getBlastMailbox()){
            $infos['default'] = MailboxesModel::eMailboxUndefault;
        }else{
            $infos['default'] = MailboxesModel::eMailboxDefault;
        }
        $mailboxModel->data($infos);
        $mailboxModel->allowField(true)->save();
        return true;
    }
    public function editMailbox($mailboxId, $infos){
        $mailboxModel = MailboxesModel::get($mailboxId);
        if(!$mailboxModel){
            exception('无法找到该邮箱设置');
        }
        $mailboxModel->data($infos);
        $mailboxModel->allowField(true)->save();
        return true;
    }
    public function deleteMailbox($mailboxId){
        $mailboxModel = MailboxesModel::get($mailboxId);
        if(!$mailboxModel){
            exception('无法找到该邮箱设置');
        }
        //系统邮箱，默认邮箱不允许删除
        if($mailboxModel->system == MailboxesModel::eMailboxSystemYes){
            exception('系统邮箱，不能删除');
        }
        if($mailboxModel->default == MailboxesModel::eMailboxDefault){
            exception('默认邮箱，不能删除');
        }
        //投递活动关联的有限不允许删除
        $campaignCount = Db::table('campaigns')->where('mailbox_id', $mailboxId)->count();
        if($campaignCount){
            exception('已关联投递活动，不能删除');
        }
        Db::table('campaigns')->where('mailbox_id', $mailboxId)->setField('mailbox_id', 0);
        $mailboxModel->delete();
        return true;
    }
    public function getMailboxInfos($mailboxId){
        $record = Db::table('mailboxes')->where(['mailbox_id'=>$mailboxId])->field(true)->find();
        return $record;
    }
    /****************************************************/
    public function getSystemMailbox(){
        $mailbox = Db::table('mailboxes')->where('system', MailboxesModel::eMailboxSystemYes)->field(true)->find();
        return $mailbox;
    }
    public function getBlastMailbox(){
        $mailbox = Db::table('mailboxes')->where('default', MailboxesModel::eMailboxDefault)->field(true)->find();
        return $mailbox;
    }
    public function getMailboxList(){
        $mailboxes = Db::table('mailboxes')->field('mailbox_id, title')->order('mailbox_id desc')->select();
        return $mailboxes;
    }
    /****************************************************/
    public function makeDefaultMailbox($mailboxId){
        Db::table('mailboxes')->where('mailbox_id', '<>', $mailboxId)->update([
            'default' => MailboxesModel::eMailboxUndefault
        ]);
        Db::table('mailboxes')->where('mailbox_id', $mailboxId)->update([
            'default' => MailboxesModel::eMailboxDefault
        ]);
        return true;
    }
    public function makeSystemMailbox($mailboxId){
        Db::table('mailboxes')->where('mailbox_id', '<>', $mailboxId)->update([
            'system' => MailboxesModel::eMailboxSystemNo
        ]);
        Db::table('mailboxes')->where('mailbox_id', $mailboxId)->update([
            'system' => MailboxesModel::eMailboxSystemYes
        ]);
        return true;
    }
    /****************************************************/
    const MAILBOX_FIELDS = [
        'mailbox_id',
        'title',
        'from_name',
        'from_email',
        'smtp_host',
        'smtp_port',
        'smtp_secure',
        'bounce_host',
        'bounce_port',
        'bounce_secure',
        'bounce_protocol',
        'account',
        'password',
        'system',
        'default'
    ];
    public function export(){
        set_time_limit(0);
        $fileName = "camellist-mailboxes-" . date('Y-m-d-H-i-s');
        header('Content-Type: application/download');
        header('Content-Type: text/csv;charset=UTF-8');
        header('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
        header('Cache-Control: max-age=0');
        $fp = fopen('php://output', 'a');
        fputcsv($fp, self::MAILBOX_FIELDS);
        $rows = Db::table('mailboxes')->select();
        foreach($rows as &$row){
            //unset($row['mailbox_id']);
            //unset($row['system']);
            //unset($row['default']);
            fputcsv($fp, array_values($row));
        }
        ob_flush();
        flush();
        fclose($fp);
    }
    public function import($saveName){
        $filePath = IMPORT_DIR . DS . $saveName;
        if(!file_exists($filePath)){
            exception("无法找到上传文件");
        }
        $f = fopen($filePath, 'r');
        if(!$f){
            exception("打开上传文件失败");
        }
        //read from file to memory
        $fields = null;
        $items = [];
        $index = 0;
        while(($data = fgetcsv($f)) !== false){
            if($index == 0){
                $fields = $data;
                if(count($fields) != count(self::MAILBOX_FIELDS)){
                    fclose($f);
                    exception("列数不符合要求");
                }
            }else{
                if(count($data) != count(self::MAILBOX_FIELDS)){
                    Log::error("忽略邮箱导入记录，列数不符合要求: " . Debug::dump($data, false));
                    continue;
                }
                $items[] = $data;
            }
            $index++;
        }
        fclose($f);
        if(empty($items)){
            //空数据，不做任何操作
            return;
        }
        //清理所有的已有数据
        Db::table('mailboxes')->where('mailbox_id', '>', 0)->delete();
        //没有field name作为key
        foreach($items as $item){
            $row = [];
            foreach($item as $key=>$value){
                $row[self::MAILBOX_FIELDS[$key]] = $value;
            }
            Db::table('mailboxes')->insert($row);
        }
        //检查system default标志
        $systemMailbox = Db::table('mailboxes')->where(['system'=>MailboxesModel::eMailboxSystemYes])->find();
        if(!$systemMailbox){
            $systemMailbox = Db::table('mailboxes')->order('mailbox_id asc')->find();//first one
            Db::table('mailboxes')->where(['mailbox_id'=>$systemMailbox['mailbox_id']])->update(['system'=>MailboxesModel::eMailboxSystemYes]);
        }

        $defaultMailbox = Db::table('mailboxes')->where(['default'=>MailboxesModel::eMailboxDefault])->find();
        if(!$defaultMailbox){
            $defaultMailbox = Db::table('mailboxes')->order('mailbox_id asc')->find();//first one
            Db::table('mailboxes')->where(['mailbox_id'=>$defaultMailbox['mailbox_id']])->update(['default'=>MailboxesModel::eMailboxDefault]);
        }
        //重置campaigns中mailbox_id设置
        $mailboxIds = Db::table('mailboxes')->distinct('mailbox_id')->column('mailbox_id');
        //$mailboxIds不可能为空
        Db::table('campaigns')->where(['mailbox_id'=>['not in', $mailboxIds]])->update(['mailbox_id'=>0]);
    }
}