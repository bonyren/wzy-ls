/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : wzyer_list

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2024-07-30 12:10:41
*/
SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE IF NOT EXISTS wzyer_list DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
use wzyer_list;
-- ----------------------------
-- Table structure for admins
-- ----------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(20) NOT NULL DEFAULT '',
  `login_password` varchar(60) NOT NULL DEFAULT '',
  `login_encrypt` varchar(10) NOT NULL DEFAULT '',
  `realname` varchar(20) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `email` varchar(60) NOT NULL DEFAULT '',
  `disabled` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-enabled, 2-disabled',
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password_changed` date NOT NULL DEFAULT '0000-00-00' COMMENT ' 密码修改日期',
  `super_user` tinyint(4) NOT NULL DEFAULT 2 COMMENT '1-super user, 2-common user',
  `role_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '角色id',
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `login_name` (`login_name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of admins
-- ----------------------------
INSERT INTO `admins` VALUES ('18', 'admin', 'c32dade41b102edfb3f5746cc8179f7b', 'xkvulT', '超级管理员', 'test@test.com', '1', '0000-00-00 00:00:00', '2024-07-30 12:03:48', '0000-00-00', '1', '1');

-- ----------------------------
-- Table structure for admin_role
-- ----------------------------
DROP TABLE IF EXISTS `admin_role`;
CREATE TABLE `admin_role` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL DEFAULT '' COMMENT '角色名称',
  `description` text NOT NULL COMMENT '角色描述',
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of admin_role
-- ----------------------------

-- ----------------------------
-- Table structure for admin_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_menu`;
CREATE TABLE `admin_role_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) unsigned DEFAULT 0,
  `menu_id` int(11) unsigned DEFAULT 0,
  `type` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '1:只读,2:读写',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of admin_role_menu
-- ----------------------------

-- ----------------------------
-- Table structure for attachments
-- ----------------------------
DROP TABLE IF EXISTS `attachments`;
CREATE TABLE `attachments` (
  `attachment_id` int(11) NOT NULL AUTO_INCREMENT,
  `original_name` varchar(255) NOT NULL DEFAULT '',
  `save_name` varchar(255) NOT NULL DEFAULT '',
  `mime_type` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `size` int(11) NOT NULL DEFAULT 0,
  `attachment_type` int(11) NOT NULL DEFAULT 1,
  `external_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联外键',
  `external_id2` int(11) NOT NULL DEFAULT 0 COMMENT '关联外键2',
  `entered` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`attachment_id`),
  KEY `idx_eid_type` (`external_id`,`attachment_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of attachments
-- ----------------------------

-- ----------------------------
-- Table structure for audit_logs
-- ----------------------------
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(40) NOT NULL DEFAULT '',
  `record_id` int(11) NOT NULL DEFAULT 0,
  `fields` varchar(255) NOT NULL DEFAULT '',
  `desc` text NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-add, 2-update, 3-delete',
  `ip` varchar(20) NOT NULL DEFAULT '',
  `device` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-computer, 2-mobile',
  `changed_by` int(11) NOT NULL DEFAULT 0,
  `entered` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `idx_m_id` (`model`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of audit_logs
-- ----------------------------

-- ----------------------------
-- Table structure for bounces
-- ----------------------------
DROP TABLE IF EXISTS `bounces`;
CREATE TABLE `bounces` (
  `bounce_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `header` text NOT NULL,
  `data` blob NOT NULL,
  `processed_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-processed, 2-unidentified',
  `comment` text NOT NULL,
  `mailbox_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`bounce_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1439 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of bounces
-- ----------------------------

-- ----------------------------
-- Table structure for bounces_campaign_subscriber
-- ----------------------------
DROP TABLE IF EXISTS `bounces_campaign_subscriber`;
CREATE TABLE `bounces_campaign_subscriber` (
  `bounces_campaign_subscriber_id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(11) NOT NULL DEFAULT 0,
  `campaign_id` int(11) NOT NULL DEFAULT 0,
  `bounce_id` int(11) NOT NULL DEFAULT 0,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`bounces_campaign_subscriber_id`)
) ENGINE=MyISAM AUTO_INCREMENT=234 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of bounces_campaign_subscriber
-- ----------------------------

-- ----------------------------
-- Table structure for campaigns
-- ----------------------------
DROP TABLE IF EXISTS `campaigns`;
CREATE TABLE `campaigns` (
  `campaign_id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `mailbox_id` int(11) NOT NULL DEFAULT 0,
  `subject` varchar(255) NOT NULL DEFAULT '(无主题)',
  `message_content` longtext NOT NULL,
  `template_id` int(11) NOT NULL DEFAULT 0,
  `send_format` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-html, 2-text',
  `embargo` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `requeue_interval` int(11) NOT NULL DEFAULT 0,
  `stop_after` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `entered` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `send_start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `send_end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `viewed_count` int(11) NOT NULL DEFAULT 0,
  `send_text_count` int(11) NOT NULL DEFAULT 0,
  `send_html_count` int(11) NOT NULL DEFAULT 0,
  `processed_count` int(11) NOT NULL DEFAULT 0,
  `bounce_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`campaign_id`),
  KEY `uuididx` (`uuid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of campaigns
-- ----------------------------

-- ----------------------------
-- Table structure for campaigns_lists
-- ----------------------------
DROP TABLE IF EXISTS `campaigns_lists`;
CREATE TABLE `campaigns_lists` (
  `campaign_list_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL DEFAULT 0,
  `list_id` int(11) NOT NULL DEFAULT 0,
  `entered` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`campaign_list_id`),
  KEY `campaign_id` (`campaign_id`) USING BTREE,
  KEY `list_id` (`list_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of campaigns_lists
-- ----------------------------

-- ----------------------------
-- Table structure for campaign_attachments
-- ----------------------------
DROP TABLE IF EXISTS `campaign_attachments`;
CREATE TABLE `campaign_attachments` (
  `campaign_attach_id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL DEFAULT 0,
  `original_name` varchar(255) NOT NULL DEFAULT '',
  `save_name` varchar(255) NOT NULL DEFAULT '',
  `mime_type` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `size` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`campaign_attach_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of campaign_attachments
-- ----------------------------

-- ----------------------------
-- Table structure for campaign_datas
-- ----------------------------
DROP TABLE IF EXISTS `campaign_datas`;
CREATE TABLE `campaign_datas` (
  `campaign_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(100) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  UNIQUE KEY `campaign_id_name` (`campaign_id`,`name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of campaign_datas
-- ----------------------------

-- ----------------------------
-- Table structure for campaign_subscribers
-- ----------------------------
DROP TABLE IF EXISTS `campaign_subscribers`;
CREATE TABLE `campaign_subscribers` (
  `campaign_id` int(11) NOT NULL DEFAULT 0,
  `subscriber_id` int(11) NOT NULL DEFAULT 0,
  `entered` timestamp NOT NULL DEFAULT current_timestamp(),
  `viewed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`campaign_id`,`subscriber_id`),
  KEY `campaign_id` (`campaign_id`) USING BTREE,
  KEY `subscriber_id` (`subscriber_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of campaign_subscribers
-- ----------------------------

-- ----------------------------
-- Table structure for event_logs
-- ----------------------------
DROP TABLE IF EXISTS `event_logs`;
CREATE TABLE `event_logs` (
  `event_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `severity` tinyint(4) NOT NULL DEFAULT 3 COMMENT '1-error,2-warning,3-info',
  `entered` timestamp NOT NULL DEFAULT current_timestamp(),
  `entry` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`event_log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=319 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of event_logs
-- ----------------------------

-- ----------------------------
-- Table structure for init_setting
-- ----------------------------
DROP TABLE IF EXISTS `init_setting`;
CREATE TABLE `init_setting` (
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of init_setting
-- ----------------------------
INSERT INTO `init_setting` VALUES ('XORMASK', 'f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562f63dd57f8253ecc0bacfc93686b62562');
INSERT INTO `init_setting` VALUES ('HMACKEY', 'fa62968c3c163d537edb59ad1ea017f64d95dbdcd3a642293537c027cb016e93dcde97e516e084bfe1ba142690ef0401d11c3e0593cf3e882539f26e790e06d7ad009482d4d47db42626ed7099c5e2c20db554876ec3f62aff06fb8048c211d4a0cd9a4a4e63f77d600a77e3b0aea1f42480ed211539d3edf9f67d373276c4ee93d8402466076037beabe0b52f6347452337429b1f69ce3419dffb119f4bc2cdda4a76edabd496c2ca1ff864993e7e19fce3f393a77d1d72dc57b668c9c30c95eac4eb3ff71146734c3e1e4b8e2a2e17fd12df5da452087e9b62d9464eda8f076ca2d967dd69b0f0b2d1aa0b9d5e57ffac05dfc383827c444c47d35f316dbc43');

-- ----------------------------
-- Table structure for job_queue
-- ----------------------------
DROP TABLE IF EXISTS `job_queue`;
CREATE TABLE `job_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scheduler_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `target` varchar(255) NOT NULL DEFAULT '',
  `execute_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `execute_end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `result` varchar(20) NOT NULL DEFAULT '',
  `message` text DEFAULT NULL,
  `client` varchar(100) NOT NULL DEFAULT '',
  `entered` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12149 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of job_queue
-- ----------------------------

-- ----------------------------
-- Table structure for link_tracks
-- ----------------------------
DROP TABLE IF EXISTS `link_tracks`;
CREATE TABLE `link_tracks` (
  `link_track_id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL DEFAULT '',
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `personalise` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`link_track_id`),
  UNIQUE KEY `urlunique` (`url`),
  KEY `urlindex` (`url`),
  KEY `uuididx` (`uuid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of link_tracks
-- ----------------------------

-- ----------------------------
-- Table structure for link_track_campaign
-- ----------------------------
DROP TABLE IF EXISTS `link_track_campaign`;
CREATE TABLE `link_track_campaign` (
  `campaign_id` int(11) NOT NULL DEFAULT 0,
  `link_track_id` int(11) NOT NULL DEFAULT 0,
  `first_click` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `latest_click` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `total` int(11) NOT NULL DEFAULT 0,
  `clicked` int(11) NOT NULL DEFAULT 0,
  `html_clicked` int(11) NOT NULL DEFAULT 0,
  `text_clicked` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`campaign_id`,`link_track_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of link_track_campaign
-- ----------------------------

-- ----------------------------
-- Table structure for link_track_subscriber
-- ----------------------------
DROP TABLE IF EXISTS `link_track_subscriber`;
CREATE TABLE `link_track_subscriber` (
  `campaign_id` int(11) NOT NULL DEFAULT 0,
  `subscriber_id` int(11) NOT NULL DEFAULT 0,
  `link_track_id` int(11) NOT NULL DEFAULT 0,
  `first_click` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `latest_click` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `clicked` int(11) NOT NULL DEFAULT 0,
  `html_clicked` int(11) NOT NULL DEFAULT 0,
  `text_clicked` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of link_track_subscriber
-- ----------------------------

-- ----------------------------
-- Table structure for lists
-- ----------------------------
DROP TABLE IF EXISTS `lists`;
CREATE TABLE `lists` (
  `list_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `entered` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-active, 2-unactive',
  `order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`list_id`),
  UNIQUE KEY `list_category` (`list_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of lists
-- ----------------------------

-- ----------------------------
-- Table structure for list_subscribers
-- ----------------------------
DROP TABLE IF EXISTS `list_subscribers`;
CREATE TABLE `list_subscribers` (
  `subscriber_id` int(11) NOT NULL DEFAULT 0,
  `list_id` int(11) NOT NULL DEFAULT 0,
  `entered` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `subscriber_list` (`subscriber_id`,`list_id`) USING BTREE,
  KEY `subscriber_id` (`subscriber_id`) USING BTREE,
  KEY `list_id` (`list_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of list_subscribers
-- ----------------------------

-- ----------------------------
-- Table structure for login_logs
-- ----------------------------
DROP TABLE IF EXISTS `login_logs`;
CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL DEFAULT '',
  `userid` int(11) NOT NULL DEFAULT 0,
  `usertype` tinyint(4) NOT NULL DEFAULT 1,
  `useragent` varchar(1024) NOT NULL DEFAULT '',
  `device` tinyint(4) NOT NULL DEFAULT 1,
  `ip` varchar(100) NOT NULL DEFAULT '',
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of login_logs
-- ----------------------------

-- ----------------------------
-- Table structure for mailboxes
-- ----------------------------
DROP TABLE IF EXISTS `mailboxes`;
CREATE TABLE `mailboxes` (
  `mailbox_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `from_name` varchar(100) NOT NULL DEFAULT '',
  `from_email` varchar(100) NOT NULL DEFAULT '',
  `smtp_host` varchar(100) NOT NULL DEFAULT '',
  `smtp_port` int(11) NOT NULL DEFAULT 25,
  `smtp_secure` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-false,2-ssl',
  `bounce_host` varchar(100) NOT NULL DEFAULT '',
  `bounce_port` int(11) NOT NULL DEFAULT 110,
  `bounce_secure` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-false,2-ssl',
  `bounce_protocol` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-pop,2-imap',
  `account` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `system` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-system,2-not system',
  `default` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-default,2-not default',
  PRIMARY KEY (`mailbox_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of mailboxes
-- ----------------------------

-- ----------------------------
-- Table structure for menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `level` tinyint(4) NOT NULL DEFAULT 1 COMMENT '层级(1,2,3)',
  `pid` smallint(6) NOT NULL DEFAULT 0 COMMENT '父id',
  `c` varchar(20) NOT NULL DEFAULT '' COMMENT '控制器',
  `a` varchar(20) NOT NULL DEFAULT '' COMMENT '方法',
  `params` varchar(64) NOT NULL DEFAULT '' COMMENT 'url附加参数',
  `icon_cls` varchar(100) NOT NULL DEFAULT '' COMMENT 'icon样式',
  `order_id` int(11) NOT NULL DEFAULT 0 COMMENT '排序号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('2', '系统管理', '1', '0', '', '', '', 'fa fa-cogs', '1000');
INSERT INTO `menu` VALUES ('3', '管理员', '2', '2', 'Admins', 'admins', '', 'fa fa-circle', '5');
INSERT INTO `menu` VALUES ('4', '管理员角色', '2', '2', 'AdminRole', 'adminRole', '', 'fa fa-circle', '6');
INSERT INTO `menu` VALUES ('5', '系统设置', '2', '2', 'System', 'setting', '', 'fa fa-circle', '1');
INSERT INTO `menu` VALUES ('71', '退信', '1', '0', '', '', '', 'fa fa-mail-reply-all', '30');
INSERT INTO `menu` VALUES ('40', '计划任务', '1', '2', 'Schedulers', 'index', '', 'fa fa-circle', '3');
INSERT INTO `menu` VALUES ('36', '登录日志', '1', '63', 'LoginLogs', 'index', '', 'fa fa-circle', '7');
INSERT INTO `menu` VALUES ('37', '已注销', '1', '7', 'Funds', 'FundsExit', '', 'fa fa-circle', '100');
INSERT INTO `menu` VALUES ('38', '变更日志', '1', '63', 'AuditLogs', 'index', '', 'fa fa-circle', '8');
INSERT INTO `menu` VALUES ('17', '导航菜单', '2', '2', 'Menus', 'index', '', 'fa fa-circle', '4');
INSERT INTO `menu` VALUES ('63', '系统状态', '1', '0', '', '', '', 'fa fa-th-large', '2000');
INSERT INTO `menu` VALUES ('64', '邮件订阅者', '1', '0', '', '', '', 'fa fa-group', '10');
INSERT INTO `menu` VALUES ('65', '总列表', '1', '64', 'Subscribers', 'subscribersSearch', '', 'fa fa-list', '1');
INSERT INTO `menu` VALUES ('66', '集合', '1', '64', 'Lists', 'layout', '', 'fa fa-th', '10');
INSERT INTO `menu` VALUES ('67', '投递活动', '1', '0', '', '', '', 'fa fa-cubes', '20');
INSERT INTO `menu` VALUES ('68', '活动列表', '1', '67', 'CampaignLists', 'campaignLists', '', 'fa fa-list', '0');
INSERT INTO `menu` VALUES ('69', '邮箱设置', '1', '67', 'Mailboxes', 'mailboxes', '', 'fa fa-inbox', '0');
INSERT INTO `menu` VALUES ('70', '模板设置', '1', '67', 'Templates', 'templates', '', 'fa fa-copy', '0');
INSERT INTO `menu` VALUES ('72', '总列表', '1', '71', 'Bounces', 'bounces', '', 'fa fa-list', '0');

-- ----------------------------
-- Table structure for messages
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(200) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `category` tinyint(4) NOT NULL DEFAULT 0,
  `is_read` tinyint(4) NOT NULL DEFAULT 0,
  `read_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `entered` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM AUTO_INCREMENT=254 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of messages
-- ----------------------------

-- ----------------------------
-- Table structure for schedulers
-- ----------------------------
DROP TABLE IF EXISTS `schedulers`;
CREATE TABLE `schedulers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `interval` varchar(100) NOT NULL DEFAULT '',
  `date_time_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_time_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `disabled` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0-enable, 1-disabled',
  `deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0-active, 1-deleted',
  `created_by` int(11) unsigned NOT NULL DEFAULT 0,
  `entered` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_schedule` (`date_time_start`,`deleted`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of schedulers
-- ----------------------------
INSERT INTO `schedulers` VALUES ('5', 'cleanServer', '清理服务器', '0 3 * * *', '2019-01-01 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '18', '0000-00-00 00:00:00');
INSERT INTO `schedulers` VALUES ('6', 'backupDB', '数据库备份', '0 2 * * *', '2019-01-01 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '18', '0000-00-00 00:00:00');
INSERT INTO `schedulers` VALUES ('7', 'processQueue', '投递邮件', '*/5 * * * *', '2019-01-01 00:00:00', '0000-00-00 00:00:00', '2023-12-06 22:25:00', '0', '0', '18', '0000-00-00 00:00:00');
INSERT INTO `schedulers` VALUES ('8', 'processBounces', '接收退信', '0 */1 * * *', '2019-01-23 00:00:00', '0000-00-00 00:00:00', '2021-11-05 16:00:01', '0', '0', '18', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for setting
-- ----------------------------
DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of setting
-- ----------------------------

-- ----------------------------
-- Table structure for subscribers
-- ----------------------------
DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE `subscribers` (
  `subscriber_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniqid` char(128) NOT NULL DEFAULT '',
  `uuid` char(128) NOT NULL DEFAULT '',
  `email` char(128) NOT NULL DEFAULT '',
  `name` char(60) NOT NULL DEFAULT '',
  `html_email` tinyint(4) NOT NULL DEFAULT 1 COMMENT '是否接受html格式邮件; 0-no, 1-yes',
  `blacklisted` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0-whitelist, 1-blacklisted',
  `deleted` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0-active, 1-deleted',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `entered` timestamp NOT NULL DEFAULT current_timestamp(),
  `bounce_count` int(11) NOT NULL DEFAULT 0 COMMENT '退信数量',
  PRIMARY KEY (`subscriber_id`),
  UNIQUE KEY `email` (`email`) USING BTREE,
  UNIQUE KEY `uniqid` (`uniqid`)
) ENGINE=MyISAM AUTO_INCREMENT=2831 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of subscribers
-- ----------------------------

-- ----------------------------
-- Table structure for templates
-- ----------------------------
DROP TABLE IF EXISTS `templates`;
CREATE TABLE `templates` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `template` longblob NOT NULL,
  `list_order` int(11) NOT NULL DEFAULT 0,
  `system` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-system,2-not system',
  `default` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`template_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of templates
-- ----------------------------
INSERT INTO `templates` VALUES ('17', '默认模板', 0x266C743B646976207374796C653D2671756F743B6D617267696E3A303B20746578742D616C69676E3A63656E7465723B2077696474683A313030253B206261636B67726F756E643A234545453B6D696E2D77696474683A32343070783B2671756F743B2667743B26616D703B6E6273703B0A266C743B646976207374796C653D2671756F743B77696474683A3936253B6D617267696E3A30206175746F3B20626F726465722D746F703A36707820736F6C696420233336393B626F726465722D626F74746F6D3A2036707820736F6C696420233336393B6261636B67726F756E643A234445463B2671756F743B2667743B0A266C743B6833207374796C653D2671756F743B6D617267696E2D746F703A3570783B6261636B67726F756E642D636F6C6F723A233639433B20666F6E742D7765696768743A6E6F726D616C3B20636F6C6F723A234646463B20746578742D616C69676E3A63656E7465723B206D617267696E2D626F74746F6D3A3570783B2070616464696E673A313070783B206C696E652D6865696768743A312E323B20666F6E742D73697A653A323170783B20746578742D7472616E73666F726D3A6361706974616C697A653B2671756F743B2667743B5B5355424A4543545D266C743B2F68332667743B0A0A266C743B646976207374796C653D2671756F743B746578742D616C69676E3A6A7573746966793B6261636B67726F756E643A234646463B70616464696E673A323070783B20626F726465722D746F703A32707820736F6C696420233336393B6D696E2D6865696768743A32303070783B666F6E742D73697A653A313370783B20626F726465722D626F74746F6D3A32707820736F6C696420233336393B2671756F743B2667743B5B434F4E54454E545D0A266C743B646976207374796C653D2671756F743B636C6561723A626F74682671756F743B2667743B26616D703B6E6273703B266C743B2F6469762667743B0A266C743B2F6469762667743B0A0A266C743B646976207374796C653D2671756F743B636C6561723A626F74683B6261636B67726F756E643A233639433B666F6E742D7765696768743A6E6F726D616C3B2070616464696E673A313070783B636F6C6F723A234646463B746578742D616C69676E3A63656E7465723B666F6E742D73697A653A313170783B6D617267696E3A357078203070782671756F743B2667743B5B464F4F5445525D266C743B6272202F2667743B0A5B5349474E41545552455D266C743B2F6469762667743B0A266C743B2F6469762667743B0A26616D703B6E6273703B266C743B2F6469762667743B, '0', '1', '1');
