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
namespace app\index\model;
use think\Model;

/*
 * CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(20) NOT NULL DEFAULT '',
  `login_password` varchar(20) NOT NULL DEFAULT '',
  `login_encrypt` varchar(10) NOT NULL DEFAULT '',
  `realname` varchar(20) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `email` varchar(60) NOT NULL DEFAULT '',
  `disabled` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-enabled, 2-disabled',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `password_changed` date NOT NULL DEFAULT '0000-00-00' COMMENT ' 密码修改日期',
  `super_user` tinyint(4) NOT NULL DEFAULT '2' COMMENT '1-super user, 2-common user',
  `role_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `login_name` (`login_name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
 */
class Admins extends Base{
    protected $pk = 'admin_id';
    protected $table = 'admins';
    /**************************************************************************/
    const eAdminSuperRole = 1;
    const eAdminCommonRole = 2;
    public static $eAdminRoleDefs = array(
        self::eAdminSuperRole=>'超级管理员',
        self::eAdminCommonRole=>'普通管理员'
    );
    const eAdminEnableStatus = 1;
    const eAdminDisabledStatus = 2;
    public static $eAdminStatusDefs = array(
        self::eAdminEnableStatus=>'有效',
        self::eAdminDisabledStatus=>'无效'
    );

    protected static $audit_fields = [
        'login_name' => '用户名',
        'realname'=>'用户真实姓名'
    ];
}