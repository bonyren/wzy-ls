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
CREATE TABLE `subscribers` (
`subscriber_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniqid` char(128) NOT NULL DEFAULT '',
  `uuid` char(128) NOT NULL DEFAULT '',
  `email` char(128) NOT NULL DEFAULT '',
  `name` char(60) NOT NULL DEFAULT '',
  `confirmed` tinyint(4) NOT NULL DEFAULT '2' COMMENT '1-confirmed, 2-not confirmed',
  `blacklisted` tinyint(4) NOT NULL DEFAULT '2' COMMENT '1-blacklisted 2-not blacklisted',
  `confirmed_manual` tinyint(4) NOT NULL DEFAULT '2' COMMENT '1-manually confirmed, 2-not manually confirmed',
  `html_email` tinyint(4) NOT NULL DEFAULT '1',
  `subscribe_page` int(11) NOT NULL DEFAULT '0',
  `entered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `bounce_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`subscriber_id`),
  UNIQUE KEY `email` (`email`) USING BTREE,
  UNIQUE KEY `uniqid` (`uniqid`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
*/
class Subscribers extends Model{
    protected $pk = 'subscriber_id';
}