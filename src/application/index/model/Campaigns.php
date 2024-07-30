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
use think\Log;
use think\Debug;
use think\Db;
/*
 * CREATE TABLE `campaigns` (
  `campaign_id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '(no subject)',
  `message_content` longtext NOT NULL,
  `message_footer` text NOT NULL,
  `template_id` int(11) NOT NULL DEFAULT '0',
  `send_format` tinyint(11) NOT NULL DEFAULT '1',
  `embargo` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `requeue_interval` int(11) NOT NULL DEFAULT '0',
  `requeue_until` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stop_after` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `send_start_alert` varchar(255) NOT NULL DEFAULT '',
  `send_end_alert` varchar(255) NOT NULL DEFAULT '',
  `entered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `send_start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `send_end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `viewed_count` int(11) NOT NULL DEFAULT '0',
  `send_text_count` int(11) NOT NULL DEFAULT '0',
  `send_html_count` int(11) NOT NULL DEFAULT '0',
  `processed_count` int(11) NOT NULL DEFAULT '0',
  `bounce_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`campaign_id`),
  KEY `uuididx` (`uuid`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

 */
class Campaigns extends Model{
    protected $pk = 'campaign_id';
}