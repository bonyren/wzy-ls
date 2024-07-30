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
namespace app\index;
class Defs{
    const LOGIN_USER_ADMIN_TYPE = 1;
    public static $loginUserTypeDefs = [
        self::LOGIN_USER_ADMIN_TYPE=>'管理员',
    ];
    public static $loginUserTypeHtmlDefs = [
        self::LOGIN_USER_ADMIN_TYPE=>'<span class="badge badge-primary">管理员</span>',
    ];
    //全局数据对象类型
    const ENTITY_FUND = 1;
    public static $entityTypes = [
        self::ENTITY_FUND => ['name'=>'基金',
            'model'=>'funds',
            'pk'=>'fund_id',
            'show'=>['url'=>'index/funds/fundsview','param'=>'fundId']],
    ];
    /** 附件状态定义 */
    const ATTACHMENT_OK = 1; //正常
    const ATTACHMENT_DEL = -1; //删除
    /***************************************************************/
    public static $iconClsDefs = [
        'ok'=>'fa fa-check',
        'cancel'=>'fa fa-close',
        'add'=>'fa fa-plus-circle',
        'edit'=>'fa fa-pencil-square',
        'delete'=>'fa fa-trash-o',
        'view'=>'fa fa-eye',
        'search'=>'fa fa-search',
        'reset'=>'fa fa-reply',
        'save'=>'fa fa-save',
    ];

    /***************************************************************/
    const eBooleanNo = 0;
    const eBooleanYes = 1;
    public static $eBooleanDefs = [
        self::eBooleanNo=>'否',
        self::eBooleanYes=>'是'
    ];
    /*
    const eSubscriberBlacklisted = 1;
    const eSubscriberNotBlacklisted = 2;
    public static $eSubscriberBlackListedDefs = array(
        self::eSubscriberBlacklisted=>'是',
        self::eSubscriberNotBlacklisted=>'否'
    );*/

    const eSubscriberHtmlEmail = 1;
    const eSubscriberTextEmail = 2;
    public static $eSubscriberHtmlEmailDefs = array(
        self::eSubscriberHtmlEmail=>'Html',
        self::eSubscriberTextEmail=>'纯文本'
    );
    const eListActive = 1;
    const eListInactive = 2;
    public static $eListActiveDefs = array(
        self::eListActive=>'有效',
        self::eListInactive=>'失效'
    );
    /*******************subscriber export fields**********************/
    const eSubscriberExportFieldId = 1;
    const eSubscriberExportFieldEmail = 2;
    const eSubscriberExportFieldBlacklisted = 3;
    const eSubscriberExportFieldEntered = 4;
    const eSubscriberExportFieldLastModified = 5;
    const eSubscriberExportFieldBounceCount = 6;

    public static $eSubscriberExportFieldDefs = array(
        self::eSubscriberExportFieldId=>'Id',
        self::eSubscriberExportFieldEmail=>'邮箱',
        self::eSubscriberExportFieldBlacklisted=>'是否黑名单',
        self::eSubscriberExportFieldEntered=>'创建时间',
        self::eSubscriberExportFieldLastModified=>'最后修改时间',
        self::eSubscriberExportFieldBounceCount=>'退信数量',
    );
    /*****************subscriber export date type********************/
    const eSubscriberExportDateAny = 1;
    const eSubscriberExportDateChanged = 2;
    public static $eSubscriberExportDateDefs = array(
        self::eSubscriberExportDateAny=>'所有日期',
        self::eSubscriberExportDateChanged=>'最新更新时间'
    );
    /*****************subscriber import********************/
    const eSubscriberImportAllLists = 0;
    public static $eSubscriberImportListsDefs = array(
        self::eSubscriberImportAllLists=>'所有的集合'
    );
    /*******************subscriber action****************************/
    const eSubscriberActionTargetTags = 1;
    const eSubscriberActionTargetAll = 2;
    public static $eSubscriberActionTargetDefs = array(
        self::eSubscriberActionTargetTags=>'所有选中的订阅者',
        self::eSubscriberActionTargetAll=>'该集合中所有订阅者'
    );
    const eSubscriberActionDelete = 1;
    const eSubscriberActionMove = 2;
    const eSubscriberActionCopy = 3;
    const eSubscriberActionNothing = 4;
    public static $eSubscriberActionDefs = array(
        self::eSubscriberActionDelete=>'从该集合中删除',
        self::eSubscriberActionMove=>'移动到',
        self::eSubscriberActionCopy=>'复制到',
        self::eSubscriberActionNothing=>'无'
    );
    /*********************campaign status****************************/
    const eCampaignStatusDraft = 1;
    const eCampaignStatusSubmitted = 2;
    const eCampaignStatusSuspended = 3;
    const eCampaignStatusInprogress = 4;
    const eCampaignStatusSent = 5;
    public static $eCampaignStatusDefs = array(
        self::eCampaignStatusDraft=>'草稿',
        self::eCampaignStatusSubmitted=>'已提交',
        self::eCampaignStatusSuspended=>'中止',
        self::eCampaignStatusInprogress=>'投递中',
        self::eCampaignStatusSent=>'已完成'
    );
    public static $eCampaignStatusHtmlDefs = array(
        self::eCampaignStatusDraft=>'<span class="badge badge-light">草稿</span>',
        self::eCampaignStatusSubmitted=>'<span class="badge badge-primary">已提交</span>',
        self::eCampaignStatusSuspended=>'<span class="badge badge-warning">中止</span>',
        self::eCampaignStatusInprogress=>'<span class="badge badge-success">投递中</span>',
        self::eCampaignStatusSent=>'<span class="badge badge-info">已完成</span>'
    );
    /*******************campaign send format************************/
    const eCampaignSendFormatHtml = 1;
    const eCampaignSendFormatText = 2;
    public static $eCampaignSendFormatDefs = array(
        self::eCampaignSendFormatHtml=>'Html格式',
        self::eCampaignSendFormatText=>'纯文本'
    );
    /*********************campaign subscriber status**************/
    const eCampaignSubscriberStatusSubmitted = 1;//prepare to send
    const eCampaignSubscriberStatusInprogress = 2;//sending in progress
    const eCampaignSubscriberStatusSentSuccess = 3;//send success
    const eCampaignSubscriberStatusInvalidEmail = 4;//invalid email
    const eCampaignSubscriberStatusSentFail = 5;//send fail

    public static $eCampaignSubscriberStatusDefs = array(
        self::eCampaignSubscriberStatusSubmitted=>'准备投递中',
        self::eCampaignSubscriberStatusInprogress=>'正在投递中',
        self::eCampaignSubscriberStatusSentSuccess=>'投递成功完成',
        self::eCampaignSubscriberStatusInvalidEmail=>'无效邮箱',
        self::eCampaignSubscriberStatusSentFail=>'投递失败'
    );
    /*********************requeue interval type******************/
    const eRequeueIntervalNot = 0;
    const eRequeueIntervalHour = 60;
    const eRequeueIntervalDay = 1440;
    const eRequeueIntervalWeek = 10080;
    public static $eRequeueIntervalDefs = array(
        self::eRequeueIntervalNot=>'--不--',
        self::eRequeueIntervalHour=>'每小时',
        self::eRequeueIntervalDay=>'每天',
        self::eRequeueIntervalWeek=>'每周'
    );
    /***********************************************************/
    const LIST_UNCATEGORIZED_NAME = '未分类';
    /**********************bounce process type***************************/
    const eBouncePending = 0;
    const eBounceSystemSubscriberMessage = 1;
    const eBounceCampaignSubscriberMessage = 2;
    const eBounceCampaignSubscriberDuplicateMessage = 3;
    const eBounceUnidentifiedCampaignSubscriberMessage = 4;
    const eBounceCampaignUnidentifiedSubscriberMessage = 5;
    const eBounceSystemUnidentifiedSubscriberMessage = 6;
    const eBounceUnidentified = 7;

    public static $eBounceStatusDefs = array(
        self::eBouncePending=>'待定',
        self::eBounceSystemSubscriberMessage=>'来自订阅者的系统退信',
        self::eBounceCampaignSubscriberMessage=>'来自订阅者的投递退信',
        self::eBounceCampaignSubscriberDuplicateMessage=>'来自订阅者的投递重复退信',
        self::eBounceUnidentifiedCampaignSubscriberMessage=>'来自订阅者的无法识别投递退信',
        self::eBounceCampaignUnidentifiedSubscriberMessage=>'无法识别订阅者的投递退信',
        self::eBounceSystemUnidentifiedSubscriberMessage=>'无法识别订阅者的系统退信',
        self::eBounceUnidentified=>'无法识别'
    );
    /*******************bounce action****************************/
    const eBounceActionTargetTags = 1;
    const eBounceActionTargetAll = 2;
    public static $eBounceActionTargetDefs = array(
        self::eBounceActionTargetTags=>'选中的退信',
        self::eBounceActionTargetAll=>'所有的退信'
    );
    const eBounceActionDeleteSubscriber = 1;
    const eBounceActionDeleteSubscriberAndBounce = 2;
    const eBounceActionBlacklistSubscriber = 3;
    const eBounceActionBlacklistSubscriberAndDeleteBounce = 4;
    const eBounceActionDeleteBounce = 5;

    public static $eBounceRegexActionDefs = [
        self::eBounceActionDeleteSubscriber=>'删除订阅者',
        self::eBounceActionDeleteSubscriberAndBounce=>'删除订阅者和退信',
        self::eBounceActionBlacklistSubscriber=>'将订阅者加入黑名单',
        self::eBounceActionBlacklistSubscriberAndDeleteBounce=>'将订阅者加入黑名单并删除退信',
        self::eBounceActionDeleteBounce=>'删除退信'
    ];
    /****************************************************************/
    const eCronResultSuccess = 1;
    const eCronResultFail = 2;
}
?>