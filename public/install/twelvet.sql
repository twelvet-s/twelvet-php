﻿# Host: localhost  (Version 5.6.44-log)
# Date: 2020-08-30 17:38:24
# Generator: MySQL-Front 6.1  (Build 1.26)


#
# Structure for table "tl_admin"
#

DROP TABLE IF EXISTS `tl_admin`;
CREATE TABLE `tl_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(12) NOT NULL COMMENT '用户名',
  `nickname` varchar(20) NOT NULL DEFAULT '管理员' COMMENT '昵称',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `password_key` char(6) NOT NULL DEFAULT '' COMMENT '密码令牌',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `token` varchar(50) DEFAULT NULL COMMENT '在线令牌',
  `login_time` int(10) DEFAULT NULL COMMENT '最近登录时间',
  `login_fail` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '登录错误次数',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` varchar(8) NOT NULL DEFAULT 'normal',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `nickname` (`nickname`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

#
# Data for table "tl_admin"
#

INSERT INTO `tl_admin` VALUES (1,'admin','Admin','c4ed63657032605738e84141c9211bb0','73518c','admin@admin.com','c2b674af-72ff-40ff-a7d5-1053f93d618d',1591246817,0,1567780884,1591246817,'normal');

#
# Structure for table "tl_admin_log"
#

DROP TABLE IF EXISTS `tl_admin_log`;
CREATE TABLE `tl_admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '管理员名字',
  `url` varchar(1500) NOT NULL DEFAULT '' COMMENT '操作页面',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '日志标题',
  `content` text NOT NULL COMMENT '内容',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP',
  `agent` varchar(255) NOT NULL DEFAULT '' COMMENT 'User-Agent',
  `createtime` int(10) DEFAULT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=249 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='管理员日志表';

#
# Data for table "tl_admin_log"
#

INSERT INTO `tl_admin_log` VALUES (243,1,'admin','/wbd7oUgiyE.php','登录','{\"username\":\"admin\",\"token\":\"84bc26452c7417dfc2e67b471e13d812\",\"captcha\":\"\"}','120.239.40.12','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0',1581249108),(244,1,'admin','/wbd7oUgiyE.php','登录','{\"username\":\"admin\",\"token\":\"f2294b97a6b78aab7381f896a08540e3\",\"captcha\":\"\"}','120.239.40.251','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36',1583221015),(245,0,'Unknown','/wbd7oUgiyE.php','登录','{\"username\":\"123\",\"token\":\"16294212064d712569704cbbf39ab77b\",\"captcha\":\"\"}','120.231.61.189','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0',1589627216),(246,0,'Unknown','/wbd7oUgiyE.php','登录','{\"username\":\"2471835953\",\"token\":\"92dbc2b7f72f10ba9eaf261c6b87c64d\",\"captcha\":\"\"}','61.140.234.239','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:77.0) Gecko/20100101 Firefox/77.0',1591246810),(247,0,'Unknown','/wbd7oUgiyE.php','登录','{\"username\":\"2471835953\",\"token\":\"c5665f889caf9bcf97c474cda690444b\",\"captcha\":\"\"}','61.140.234.239','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:77.0) Gecko/20100101 Firefox/77.0',1591246815),(248,1,'admin','/wbd7oUgiyE.php','登录','{\"username\":\"admin\",\"token\":\"e8b548c703da7dae4a02f9ed983c0a57\",\"captcha\":\"\"}','61.140.234.239','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:77.0) Gecko/20100101 Firefox/77.0',1591246817);

#
# Structure for table "tl_auth_group"
#

DROP TABLE IF EXISTS `tl_auth_group`;
CREATE TABLE `tl_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父组别',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '组名',
  `rules` text NOT NULL COMMENT '规则ID',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `status` varchar(30) NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='分组表';

#
# Data for table "tl_auth_group"
#

INSERT INTO `tl_auth_group` VALUES (1,0,'Admin group','*',1490883540,149088354,'normal'),(2,1,'Second group','13,14,16,15,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,40,41,42,43,44,45,46,47,48,49,50,55,56,57,58,59,60,61,62,63,64,65,1,9,10,11,7,6,8,2,4,5',1490883540,1505465692,'normal'),(3,2,'Third group','1,4,9,10,11,13,14,15,16,17,40,41,42,43,44,45,46,47,48,49,50,55,56,57,58,59,60,61,62,63,64,65,5',1490883540,1502205322,'normal'),(4,1,'Second group 2','1,4,13,14,15,16,17,55,56,57,58,59,60,61,62,63,64,65',1490883540,1502205350,'normal'),(5,2,'Third group 2','1,2,6,7,8,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34',1490883540,1502205344,'normal');

#
# Structure for table "tl_auth_group_access"
#

DROP TABLE IF EXISTS `tl_auth_group_access`;
CREATE TABLE `tl_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `group_id` int(10) unsigned NOT NULL COMMENT '级别ID',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='权限分组表';

#
# Data for table "tl_auth_group_access"
#

INSERT INTO `tl_auth_group_access` VALUES (1,1);

#
# Structure for table "tl_auth_rule"
#

DROP TABLE IF EXISTS `tl_auth_rule`;
CREATE TABLE `tl_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('menu','file') NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '图标',
  `condition` varchar(255) NOT NULL DEFAULT '' COMMENT '条件',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为菜单',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `pid` (`pid`),
  KEY `weigh` (`weigh`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='节点表';

#
# Data for table "tl_auth_rule"
#

INSERT INTO `tl_auth_rule` VALUES (1,'file',0,'addon','Addon','fa fa-rocket','','Addon tips',1,1502035509,1502035509,0,'normal');

#
# Structure for table "tl_user"
#

DROP TABLE IF EXISTS `tl_user`;
CREATE TABLE `tl_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(12) NOT NULL COMMENT '用户名',
  `nickname` varchar(20) NOT NULL DEFAULT '新用户' COMMENT '昵称',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `password_key` char(6) NOT NULL DEFAULT '' COMMENT '密码令牌',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `token` varchar(50) NOT NULL DEFAULT '' COMMENT '在线令牌',
  `login_ip` varchar(30) DEFAULT NULL COMMENT '登录ip',
  `login_time` int(10) DEFAULT NULL COMMENT '最近登录时间',
  `login_fail` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '登录错误次数',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `status` varchar(8) NOT NULL DEFAULT 'normal',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `nickname` (`nickname`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for table "tl_user"
#

