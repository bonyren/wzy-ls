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
return array(
    /* 全站设置  */
    'SYSTEM_TITLE' => array(
        'name'    => '系统标题',
        'group'   => '系统',
        'editor'  => array('type'=>'textbox','options'=>array('tipPosition'=>'left', 'validType'=>array('nothtml','length[0,255]'))),
        'default' => 'CamelList',
    ),
    'SYSTEM_KEYWORDS' => array(
        'name'    => '系统关键字',
        'group'   => '系统',
        'editor'  => array('type'=>'textbox','options'=>array('tipPosition'=>'left', 'validType'=>array('nothtml','length[0,255]'))),
        'default' => 'CamelList',
    ),
    'SYSTEM_DESCRIPTION' => array(
        'name'    => '系统描述',
        'group'   => '系统',
        'editor'  => array('type'=>'textbox','options'=>array('tipPosition'=>'left', 'validType'=>array('nothtml','length[0,255]'))),
        'default' => 'CamelList',
    ),
    'ADMIN_EMAIL' => array(
        'name'    => '管理员邮箱',
        'group'   => '系统',
        'editor'  => array('type'=>'textbox','options'=>array('tipPosition'=>'left', 'validType'=>array('nothtml','email','length[0,255]'))),
        'default' => '',
    ),
    'ORGANIZATION_NAME' => array(
        'name'    => '组织名字',
        'group'   => '系统',
        'editor'  => array('type'=>'textbox','options'=>array('tipPosition'=>'left', 'validType'=>array('nothtml','length[0,255]'))),
        'default' => 'CamelList',
    ),
    'ORGANIZATION_LOGO' => array(
        'name'    => '系统LOGO',
        'group'   => '系统',
        'editor'  => array('type'=>'image','options'=>array( 'handler'=>'systemSettingModule.image', 'zoom'=>false)),
        'default' => '/static/img/logo.png',
    ),
    'POWER_BY_TEXT' => array(
        'name'    => '版权信息',
        'group'   => '系统',
        'editor'  => array('type'=>'textbox','options'=>array('tipPosition'=>'left', 'validType'=>array('nothtml','length[0,255]'))),
        'default' => 'powered by CamelList',
    ),
    'LOGIN_ONLY_ONE' => array(
        'name'    => '单点登录',
        'group'   => '操作设置',
        'editor'  => array('type'=>'checkbox','options'=>array('on'=>'yes','off'=>'no')),
        'default' => 'no'
    ),
    /* 邮箱设置  */
    /*
    'EMAIL_SMTP' => array(
        'name'    => 'SMTP',
        'group'   => '邮箱设置',
        'editor'  => array('type'=>'validatebox','options'=>array('tipPosition'=>'left', 'validType'=>array('nothtml','length[0,255]'))),
        'default' => ''
    ),
    'EMAIL_PORT' => array(
        'name'    => '端口',
        'group'   => '邮箱设置',
        'editor'  => 'numberbox',
        'default' => 25
    ),
    'EMAIL_USER' => array(
        'name'    => '用户名',
        'group'   => '邮箱设置',
        'editor'  => array('type'=>'validatebox','options'=>array('tipPosition'=>'left', 'validType'=>array('nothtml','length[0,255]'))),
        'default' => ''
    ),
    'EMAIL_PWD' => array(
        'name'    => '密码',
        'group'   => '邮箱设置',
        'editor'  => array('type'=>'validatebox','options'=>array('tipPosition'=>'left', 'validType'=>array('nothtml','length[0,255]'))),
        'default' => ''
    ),
    'EMAIL_FROM_ADDRESS' => array(
        'name'    => '发信地址',
        'group'   => '邮箱设置',
        'editor'  => array('type'=>'validatebox','options'=>array('tipPosition'=>'left', 'validType'=>array('nothtml','length[0,255]'))),
        'default' => ''
    ),
    'EMAIL_FROM_NAME' => array(
        'name'    => '发信名字',
        'group'   => '邮箱设置',
        'editor'  => array('type'=>'validatebox','options'=>array('tipPosition'=>'left', 'validType'=>array('nothtml','length[0,255]'))),
        'default' => ''
    ),*/
    /**************************************************************************************************************/
    'CAMPAIGN_NOTIFYSTART' => array(
        'name'    => '投递开始时接收通知的邮箱',
        'group'   => '投递活动设置',
        'editor'  => array('type'=>'textbox','options'=>array('tipPosition'=>'left', 'validType'=>array('nothtml','email','length[0,255]'))),
        'default' => '',
    ),
    'CAMPAIGN_NOTIFYEND' => array(
        'name'    => '投递结束时接收通知的邮箱',
        'group'   => '投递活动设置',
        'editor'  => array('type'=>'textbox','options'=>array('tipPosition'=>'left', 'validType'=>array('nothtml','email','length[0,255]'))),
        'default' => '',
    ),
    'CAMPAIGN_MESSAGEFOOTER' => array(
        'name'    => '投递邮件的内容页脚',
        'group'   => '投递活动设置',
        'editor'  => array('type'=>'textbox','options'=>array('multiline'=>true,'height'=>200)),
        'default' => '<p>This message was sent to [EMAIL] by [FROMEMAIL]</p>',
    ),
    /**************************************************************************************************************/
    /* 数据备份  */
    'DB_BACKUP_PATH' => array(
        'name'    => '备份路径',
        'group'   => '数据备份',
        'editor'  => array('type'=>'validatebox','options'=>array('tipPosition'=>'left', 'validType'=>array('nothtml','length[0,255]'))),
        'default' => ROOT_PATH . 'db' . DS . 'backup'
    ),
    'DB_BACKUP_EXPIRATION' => array(
        'name'    => '保留天数',
        'group'   => '数据备份',
        'editor'  => 'numberbox',
        'default' => 30
    ),
);