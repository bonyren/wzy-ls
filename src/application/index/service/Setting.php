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
namespace app\index\service;
use app\index\model\Setting as settingModel;
class Setting extends Base
{
    protected $_model;
    protected function __construct() {
        parent::__construct();
        $this->_model = new settingModel();
    }

    public function setting($field){
        return $this->_model->getSetting($field);
    }
}