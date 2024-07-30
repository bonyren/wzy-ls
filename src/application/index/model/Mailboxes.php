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
/*
 * CREATE TABLE `mailboxes` (
  `mailbox_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `smtp_host` varchar(100) NOT NULL DEFAULT '',
  `smtp_port` int(11) NOT NULL DEFAULT '25',
  `smtp_secure` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-false,2-ssl',
  `bounce_host` varchar(100) NOT NULL DEFAULT '',
  `bounce_port` int(11) NOT NULL DEFAULT '110',
  `bounce_secure` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-false,2-ssl',
  `bounce_protocol` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-pop,2-imap',
  `account` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `system` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-system,2-not system',
  `default` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-default,2-not default',
  PRIMARY KEY (`mailbox_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
 */
namespace app\index\model;
use think\Model;
use think\Debug;
use think\Log;

class Mailboxes extends model{
    protected $pk = 'mailbox_id';
    protected $table = 'mailboxes';

    const eMailboxSecureFalse = 1;
    const eMailboxSecureSSL = 2;
    public static $eMailboxSecureDefs = [
        self::eMailboxSecureFalse=>'false',
        self::eMailboxSecureSSL=>'ssl'
    ];

    const eMailboxProtocolPop = 1;
    const eMailboxProtocoImap = 2;
    public static $eMailboxProtocolDefs = [
        self::eMailboxProtocolPop=>'pop',
        self::eMailboxProtocoImap=>'imap'
    ];

    const eMailboxSystemYes = 1;
    const eMailboxSystemNo = 2;
    public static $eMailboxSystemDefs = [
        self::eMailboxSystemYes=>'yes',
        self::eMailboxSystemNo=>'no'
    ];
    const eMailboxDefault = 1;
    const eMailboxUndefault = 2;
    public static $eMailboxDefaultDefs = [
        self::eMailboxDefault=>'Default',
        self::eMailboxUndefault=>'Undefault'
    ];
}