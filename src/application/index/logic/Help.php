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

class Help extends Base{
    const HELP_TOPIC_EBAY_PRODUCT_ITEM_NUMBER = 1;
    public static $helpTopicDefs = [
        self::HELP_TOPIC_EBAY_PRODUCT_ITEM_NUMBER=>[
            'title'=>'如何找到eBay产品编号?',
            'tpl'=>'ebay_product_item_number'
        ]
    ];
    public function __construct(){
        parent::__construct();
    }
    public function getText($topicId){
        if(!array_key_exists($topicId, self::$helpTopicDefs)){
            return '';
        }
        return sprintf('<a href="#" onclick="return window.baseModule.help(%d)">%s</a>', $topicId, self::$helpTopicDefs[$topicId]['title']);
    }
    public function getTpl($topicId){
        if(!array_key_exists($topicId, self::$helpTopicDefs)){
            return false;
        }
        return self::$helpTopicDefs[$topicId]['tpl'];
    }
}