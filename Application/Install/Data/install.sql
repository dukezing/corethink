# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.6.21)
# Database: corethink
# Generation Time: 2015-05-23 11:19:04 +0000
# ************************************************************


# Dump of table ct_addon
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_addon`;

CREATE TABLE `ct_addon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '插件名或标识',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text NOT NULL COMMENT '插件描述',
  `config` text COMMENT '配置',
  `author` varchar(32) NOT NULL DEFAULT '' COMMENT '作者',
  `version` varchar(8) NOT NULL DEFAULT '' COMMENT '版本号',
  `adminlist` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台列表',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `utime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='插件表';

LOCK TABLES `ct_addon` WRITE;
/*!40000 ALTER TABLE `ct_addon` DISABLE KEYS */;

INSERT INTO `ct_addon` (`id`, `name`, `title`, `description`, `config`, `author`, `version`, `adminlist`, `ctime`, `utime`, `sort`, `status`)
VALUES
	(1,'ReturnTop','返回顶部','返回顶部','{\"status\":\"1\",\"theme\":\"rocket\",\"customer\":\"\",\"case\":\"\",\"qq\":\"\",\"weibo\":\"\"}','CoreThink','1.0',0,1407681961,1408602081,0,1),
	(2,'Email','邮件插件','实现系统发邮件功能','{\"status\":\"1\",\"MAIL_SMTP_TYPE\":\"1\",\"MAIL_SMTP_SECURE\":\"0\",\"MAIL_SMTP_PORT\":\"25\",\"MAIL_SMTP_HOST\":\"smtp.qq.com\",\"MAIL_SMTP_USER\":\"\",\"MAIL_SMTP_PASS\":\"\",\"default\":\"\"}','CoreThink','1.0',0,1428732454,1428732454,0,1),
	(3,'SyncLogin','第三方账号登陆','第三方账号登陆','{\"type\":[\"Qq\",\"Sina\",\"Renren\"],\"meta\":\"\",\"QqKEY\":\"\",\"QqSecret\":\"\",\"SinaKEY\":\"\",\"SinaSecret\":\"\",\"RenrenKEY\":\"\",\"RenrenSecret\":\"\",\"GoogleKEY\":\"\",\"GoogleSecret\":\"\"}','CoreThink','1.0',1,1428250248,1428250248,0,1),
	(4,'AdFloat','图片漂浮广告','图片漂浮广告','{\"status\":\"0\",\"url\":\"http:\\/\\/www.corethink.cn\",\"image\":\"\",\"width\":\"100\",\"height\":\"100\",\"speed\":\"10\",\"target\":\"1\"}','CoreThink','1.0',0,1408602081,1408602081,0,1);

/*!40000 ALTER TABLE `ct_addon` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ct_addon_hook
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_addon_hook`;

CREATE TABLE `ct_addon_hook` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '钩子ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `description` text NOT NULL COMMENT '描述',
  `addons` varchar(255) NOT NULL COMMENT '钩子挂载的插件 ''，''分割',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `utime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='钩子表';

LOCK TABLES `ct_addon_hook` WRITE;
/*!40000 ALTER TABLE `ct_addon_hook` DISABLE KEYS */;

INSERT INTO `ct_addon_hook` (`id`, `name`, `description`, `addons`, `type`, `ctime`, `utime`, `status`)
VALUES
	(1,'PageHeader','页面header钩子，一般用于加载插件CSS文件和代码','SyncLogin',1,1407681961,1407681961,1),
	(2,'PageFooter','页面footer钩子，一般用于加载插件CSS文件和代码','ReturnTop,AdFloat',1,1407681961,1407681961,1),
	(3,'SyncLogin','第三方登陆','SyncLogin',1,1407681961,1407681961,1);

/*!40000 ALTER TABLE `ct_addon_hook` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ct_addon_sync_login
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_addon_sync_login`;

CREATE TABLE `ct_addon_sync_login` (
  `uid` int(11) NOT NULL COMMENT 'ID',
  `openid` varchar(64) NOT NULL DEFAULT '' COMMENT 'OpenID',
  `type` varchar(15) NOT NULL DEFAULT '' COMMENT '类别',
  `access_token` varchar(64) NOT NULL DEFAULT '' COMMENT 'AccessToken',
  `refresh_token` varchar(64) NOT NULL DEFAULT '' COMMENT 'RefreshToken'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='第三方登陆插件表';



# Dump of table ct_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_category`;

CREATE TABLE `ct_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父分类ID',
  `doc_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '分类模型',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '分类名称',
  `url` varchar(128) NOT NULL COMMENT '链接地址',
  `content` text NOT NULL COMMENT '内容',
  `template` varchar(32) NOT NULL DEFAULT '' COMMENT '模版',
  `icon` varchar(32) NOT NULL DEFAULT '' COMMENT '缩略图',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `utime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='栏目分类表';

LOCK TABLES `ct_category` WRITE;
/*!40000 ALTER TABLE `ct_category` DISABLE KEYS */;

INSERT INTO `ct_category` (`id`, `pid`, `doc_type`, `title`, `url`, `content`, `template`, `icon`, `ctime`, `utime`, `sort`, `status`)
VALUES
	(1,0,3,'默认','','','','icon-location-arrow',1431926468,1431926468,0,1);

/*!40000 ALTER TABLE `ct_category` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ct_document
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_document`;

CREATE TABLE `ct_document` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `cid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发布者ID',
  `title` char(127) NOT NULL DEFAULT '' COMMENT '标题',
  `view` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '阅读量',
  `comment` int(11) NOT NULL DEFAULT '0' COMMENT '评论数',
  `good` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '赞数',
  `bad` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '踩数',
  `mark` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收藏',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `utime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文档类型基础表';



# Dump of table ct_document_attribute
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_document_attribute`;

CREATE TABLE `ct_document_attribute` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '字段名',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '字段标题',
  `field` varchar(100) NOT NULL DEFAULT '' COMMENT '字段定义',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '数据类型',
  `value` varchar(100) NOT NULL DEFAULT '' COMMENT '字段默认值',
  `tip` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `options` varchar(255) NOT NULL DEFAULT '' COMMENT '参数',
  `doc_type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '文档模型',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `utime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文档属性字段表';

LOCK TABLES `ct_document_attribute` WRITE;
/*!40000 ALTER TABLE `ct_document_attribute` DISABLE KEYS */;

INSERT INTO `ct_document_attribute` (`id`, `name`, `title`, `field`, `type`, `value`, `tip`, `show`, `options`, `doc_type`, `ctime`, `utime`, `status`)
VALUES
	(1, 'cid', '分类', 'int(11) unsigned NOT NULL ', 'select', '0', '所属分类', 1, '', 0, 1383891233, 1384508336, 1),
	(2, 'uid', '用户ID', 'int(11) unsigned NOT NULL ', 'num', '0', '用户ID', 0, '', 0, 1383891233, 1384508336, 1),
	(3, 'title', '标题', 'char(127) NOT NULL ', 'text', '', '文档标题', 1, '', 0, 1383891233, 1383894778, 1),
	(4, 'view', '阅读量', 'varchar(255) NOT NULL', 'num', '0', '标签', 0, '', 0, 1413303715, 1413303715, 1),
	(5, 'comment', '评论数', 'int(11) unsigned NOT NULL ', 'num', '0', '评论数', 0, '', 0, 1383891233, 1383894927, 1),
	(6, 'good', '赞数', 'int(11) unsigned NOT NULL ', 'num', '0', '赞数', 0, '', 0, 1383891233, 1384147827, 1),
	(7, 'bad', '踩数', 'int(11) unsigned NOT NULL ', 'num', '0', '踩数', 0, '', 0, 1407646362, 1407646362, 1),
	(8, 'ctime', '创建时间', 'int(11) unsigned NOT NULL ', 'time', '0', '创建时间', 1, '', 0, 1383891233, 1383895903, 1),
	(9, 'utime', '更新时间', 'int(11) unsigned NOT NULL ', 'time', '0', '更新时间', 0, '', 0, 1383891233, 1384508277, 1),
	(10, 'sort', '排序', 'int(11) unsigned NOT NULL ', 'num', '0', '用于显示的顺序', 1, '', 0, 1383891233, 1383895757, 1),
	(11, 'status', '数据状态', 'tinyint(4) NOT NULL ', 'radio', '1', '', 0, '-1:删除\r\n0:禁用\r\n1:正常', 0, 1383891233, 1384508496, 1),
	(12, 'abstract', '简介', 'vachar(255) NOT NULL', 'textarea', '', '文档简介', 1, '', 3, 1383891233, 1384508496, 1),
	(13, 'content', '正文内容', 'text', 'kindeditor', '', '文章正文内容', 1, '', 3, 1383891233, 1384508496, 1),
	(14, 'tags', '文章标签', 'vachar(128) NOT NULL', 'tags', '', '标签', 1, '', 3, 1383891233, 1384508496, 1),
	(15, 'cover', '封面', 'int(11) unsigned NOT NULL ', 'picture', '0', '文档封面', 1, '', 3, 1383891233, 1384508496, 1);

/*!40000 ALTER TABLE `ct_document_attribute` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ct_document_extend_article
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_document_extend_article`;

CREATE TABLE `ct_document_extend_article` (
  `id` int(11) unsigned NOT NULL COMMENT '文档ID',
  `tags` varchar(128) NOT NULL DEFAULT '' COMMENT '标签',
  `abstract` varchar(255) NOT NULL DEFAULT '' COMMENT '简介',
  `content` text NOT NULL COMMENT '正文内容',
  `cover` int(11) NOT NULL DEFAULT '0' COMMENT '封面图片ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章类型扩展表';



# Dump of table ct_document_type
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_document_type`;

CREATE TABLE `ct_document_type` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '模型ID',
  `name` char(16) NOT NULL DEFAULT '' COMMENT '模型名称',
  `title` char(16) NOT NULL DEFAULT '' COMMENT '模型标题',
  `icon` varchar(32) NOT NULL DEFAULT '' COMMENT '缩略图',
  `field_sort` text NOT NULL COMMENT '表单字段排序',
  `field_group` varchar(255) NOT NULL DEFAULT '' COMMENT '表单字段分组',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `utime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文档模型表';

LOCK TABLES `ct_document_type` WRITE;
/*!40000 ALTER TABLE `ct_document_type` DISABLE KEYS */;

INSERT INTO `ct_document_type` (`id`, `name`, `title`, `icon`, `field_sort`, `field_group`, `ctime`, `utime`, `sort`, `status`)
VALUES
	(1,'Link','链接','icon-link','','',1426580628,1426580628,0,1),
	(2,'Page','单页','icon-file','','',1426580628,1426580628,0,1),
	(3,'Article','文章','icon-edit','{\"1\":[\"1\",\"3\",\"12\",\"13\",\"14\",\"15\"],\"2\":[\"10\",\"8\"]}','1:基础\n2:扩展',1426580628,1426580628,0,1);

/*!40000 ALTER TABLE `ct_document_type` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ct_system_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_system_config`;

CREATE TABLE `ct_system_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '配置标题',
  `name` varchar(32) NOT NULL COMMENT '配置名称',
  `value` text NOT NULL COMMENT '配置值',
  `group` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `type` varchar(16) NOT NULL DEFAULT '' COMMENT '配置类型',
  `options` varchar(255) NOT NULL DEFAULT '' COMMENT '配置额外值',
  `tip` varchar(100) NOT NULL DEFAULT '' COMMENT '配置说明',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `utime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统配置表';

LOCK TABLES `ct_system_config` WRITE;
/*!40000 ALTER TABLE `ct_system_config` DISABLE KEYS */;

INSERT INTO `ct_system_config` (`id`, `title`, `name`, `value`, `group`, `type`, `options`, `tip`, `ctime`, `utime`, `sort`, `status`)
VALUES
	(1,'站点开关','TOGGLE_WEB_SITE','1',1,'select','0:关闭,1:开启','站点关闭后将不能访问',1378898976,1406992386,1,1),
	(2,'网站标题','WEB_SITE_TITLE','CoreThink框架',1,'text','','网站标题前台显示标题',1378898976,1379235274,2,1),
	(3,'网站LOGO','WEB_SITE_LOGO','',1,'picture','','网站LOGO',1407003397,1407004692,3,1),
	(4,'网站描述','WEB_SITE_DESCRIPTION','CoreThink是一套轻量级WEB产品开发框架，追求简单、高效、卓越。可轻松实现移动互联网时代支持多终端的轻量级WEB产品快速开发。系统功能采用模块化开发，内置丰富的模块，便于用户灵活扩展和二次开发。',1,'textarea','','网站搜索引擎描述',1378898976,1379235841,4,1),
	(5,'网站关键字','WEB_SITE_KEYWORD','南京科斯克网络科技、CoreThink',1,'textarea','','网站搜索引擎关键字',1378898976,1381390100,5,1),
	(6,'版权信息','WEB_SITE_COPYRIGHT','版权所有 © 2014-2015 科斯克网络科技',1,'text','','设置在网站底部显示的版权信息，如“版权所有 © 2014-2015 科斯克网络科技”',1406991855,1406992583,6,1),
	(7,'网站备案号','WEB_SITE_ICP','苏ICP备15000000号',1,'text','','设置在网站底部显示的备案号，如“苏ICP备14000000号\"',1378900335,1415983236,7,1),
	(8,'站点统计','WEB_SITE_STATISTICS','',1,'textarea','','支持百度、Google、cnzz等所有Javascript的统计代码',1407824190,1407824303,8,1),
	(9,'前台主题','DEFAULT_THEME','default',1,'select','default:默认','前台模版主题，不影响后台',1425215616,1425299454,9,1),
	(10,'注册开关','TOGGLE_USER_REGISTER','1',2,'select','0:关闭注册\r\n1:允许注册','是否开放用户注册',1379504487,1379504580,2,1),
	(11,'注册时间间隔','LIMIT_TIME_BY_IP','300',2,'num','','同一IP注册时间间隔秒数',1379228036,1379228036,2,1),
	(12,'评论开关','TOGGLE_USER_COMMENT','1',2,'select','0:关闭评论,1:允许评论','评论关闭后用户不能进行评论',1418715779,1418716106,3,1),
	(13,'文件上传大小','UPLOAD_FILE_SIZE','10',2,'num','','文件上传大小单位：MB',1428681031,1428681031,4,1),
	(14,'图片上传大小','UPLOAD_IMAGE_SIZE','2',2,'num','','图片上传大小单位：MB',1428681071,1428681071,5,1),
	(15,'敏感字词','SENSITIVE_WORDS','傻逼,垃圾',2,'textarea','','用户注册及内容显示敏感字词',1420385145,1420387079,6,1),
	(16,'是否显示页面Trace','SHOW_PAGE_TRACE','0',3,'select','0:关闭\r\n1:开启','是否显示页面Trace信息',1387165685,1387165685,1,1),
	(17,'开发模式', 'DEVELOP_MODE', '1', 3, 'select', '1:开启\r\n0:关闭', '开发模式下会显示菜单管理、配置管理、数据字典等开发者工具', 1432393583, 1432393583, 2, 1),
	(18,'配置分组','CONFIG_GROUP_LIST','1:基本\r\n2:用户\r\n3:系统\r\n4:上传\r\n',3,'array','','配置分组',1379228036,1426930700,3,1),
	(19,'文件上传驱动类型','UPLOAD_DRIVER','Local',4,'select','Local:Local-本地\r\nFtp:FTP空间\r\nSae:Sae-Storage\r\nBcs:Bcs云存储\r\nUpyun:又拍云\r\nQiniu:七牛云存储','需要配置相应的UPLOAD_{driver}_CONFIG 配置方可使用，不然默认Local本地',1393073505,1393073505,1,1),
	(20,'FTP上传配置','UPLOAD_FTP_CONFIG','host:\r\nusername:\r\npassword:',4,'array','','FTP上传配置',1393073559,1393073559,2,1),
	(21,'Sae上传配置','UPLOAD_SAE_CONFIG','domain:',4,'array','','Sae上传配置',1393073998,1393073998,3,1),
	(22,'Bcs上传配置','UPLOAD_BCS_CONFIG','AccessKey:\r\nSecretKey:\r\nbucket:',4,'array','','Bcs上传配置',1393073559,1393073559,4,1),
	(23,'又拍云上传配置','UPLOAD_UPYUN_CONFIG','host:\r\nusername:\r\npassword:\r\nbucket:',4,'array','','又拍云上传配置',1393073559,1393073559,5,1),
	(24,'七牛云存储上传配置','UPLOAD_QINIU_CONFIG','secrectKey:\r\naccessKey:\r\ndomain:\r\nbucket:',4,'array','','七牛云存储上传配置',1393074989,1416637334,6,1);


/*!40000 ALTER TABLE `ct_system_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ct_system_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_system_menu`;

CREATE TABLE `ct_system_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上级菜单ID',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `url` varchar(128) NOT NULL DEFAULT '' COMMENT '链接地址',
  `icon` varchar(32) NOT NULL COMMENT '图标',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `utime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='后台菜单表';

LOCK TABLES `ct_system_menu` WRITE;
/*!40000 ALTER TABLE `ct_system_menu` DISABLE KEYS */;

INSERT INTO `ct_system_menu` (`id`, `pid`, `title`, `url`, `icon`, `ctime`, `utime`, `sort`, `status`)
VALUES
	(1,0,'首页','Index/index','icon-home',1426580628,1426580628,1,1),
	(2,0,'系统','SystemConfig/group','icon-windows',1426580628,1426580628,2,1),
	(3,0,'内容','Category/index','icon-tasks',1430290092,1430291772,2,1),
	(4,0,'用户','User/index','icon-group',1426580628,1426580628,3,1),
	(5,0,'其它','','icon-cloud',1426580628,1426580628,3,1),
	(6,1,'系统操作','','icon-folder-open-alt',1426580628,1426580628,1,1),
	(7,2,'系统功能','','icon-folder-open-alt',1426580628,1426580628,1,1),
	(8,2,'数据备份','','icon-folder-open-alt',1426580628,1426580628,2,1),
	(9,3,'文档管理','','icon-folder-open-alt',1430290276,1430291485,1,1),
	(10,3,'文件管理','','icon-folder-open-alt',1430290276,1430291485,1,1),
	(11,4,'用户管理','','icon-folder-open-alt',1426580628,1426580628,1,1),
	(12,6,'清空缓存','Index/rmdirr','',1427475588,1427475588,1,1),
	(13,7,'系统设置','SystemConfig/group','icon-cog',1426580628,1430291269,1,1),
	(14,13,'修改','SystemConfig/groupSave','',1426580628,1426580628,1,1),
	(15,7,'文档类型','DocumentType/index','icon-th',1426580628,1430291065,2,1),
	(16,15,'添加','DocumentType/add','',1426580628,1426580628,1,1),
	(17,15,'编辑','DocumentType/edit','',1426580628,1426580628,2,1),
	(18,15,'设置状态','DocumentType/setStatus','',1426580628,1426580628,3,1),
	(19,15,'字段管理','DocumentAttribute/index','icon-reorder',1426580628,1430291065,1,1),
	(20,19,'添加','DocumentAttribute/add','',1426580628,1426580628,1,1),
	(21,19,'编辑','DocumentAttribute/edit','',1426580628,1426580628,2,1),
	(22,19,'设置状态','DocumentAttribute/setStatus','',1426580628,1426580628,3,1),
	(23,7,'菜单管理','SystemMenu/index','icon-reorder',1426580628,1430291065,3,1),
	(24,23,'添加','SystemMenu/add','',1426580628,1426580628,1,1),
	(25,23,'编辑','SystemMenu/edit','',1426580628,1426580628,2,1),
	(26,23,'设置状态','SystemMenu/setStatus','',1426580628,1426580628,3,1),
	(27,7,'配置管理','SystemConfig/index','icon-wrench',1426580628,1430291167,4,1),
	(28,27,'添加','SystemConfig/add','',1426580628,1426580628,1,1),
	(29,27,'编辑','SystemConfig/edit','',1426580628,1426580628,2,1),
	(30,27,'设置状态','SystemConfig/setStatus','',1426580628,1426580628,3,1),
	(31,7,'数据字典','Datebase/index','icon-table',1429851071,1430291185,5,1),
	(32,7,'插件列表','Addon/index','icon-cogs',1427475588,1427475588,6,1),
	(33,31,'安装','Addon/install','',1427475588,1427475588,1,1),
	(34,31,'卸载','Addon/uninstall','',1427475588,1427475588,2,1),
	(35,31,'执行','Addon/execute','',1427475588,1427475588,3,1),
	(36,31,'插件设置','Addon/config','',1427475588,1427475588,4,1),
	(37,31,'数据列表','Addon/adminList','',1427475588,1427475588,6,1),
	(38,8,'数据备份','Datebase/export','icon-archive',1426580628,1426580628,1,1),
	(39,38,'备份','Datebase/do_export','',1426580628,1426580628,1,1),
	(40,38,'优化表','Datebase/optimize','',1426580628,1426580628,2,1),
	(41,38,'修复表','Datebase/repair','',1426580628,1426580628,3,1),
	(42,8,'数据还原','Datebase/import','icon-undo',1426580628,1426580628,2,1),
	(43,42,'还原备份','Datebase/do_import','',1426580628,1426580628,1,1),
	(44,42,'删除备份','Datebase/del','',1426580628,1426580628,2,1),
	(45,9,'栏目分类','Category/index','icon-branch',1426580628,1430290312,1,1),
	(46,45,'添加','Category/add','',1426580628,1426580628,1,1),
	(47,45,'编辑','Category/edit','',1426580628,1426580628,2,1),
	(48,45,'设置状态','Category/setStatus','',1426580628,1426580628,3,1),
	(50,45,'文档列表','Document/index','',1427475588,1427475588,4,1),
	(51,50,'添加','Document/add','',1426580628,1426580628,1,1),
	(52,50,'编辑','Document/edit','',1426580628,1426580628,2,1),
	(53,50,'设置状态','Document/setStatus','',1426580628,1426580628,3,1),
	(54,9,'标签列表','Tag/index','icon-tag',1426580628,1430290718,3,1),
	(55,54,'添加','Tag/add','',1426580628,1426580628,1,1),
	(56,54,'编辑','Tag/edit','',1426580628,1426580628,2,1),
	(57,54,'设置状态','Tag/setStatus','',1426580628,1426580628,3,1),
	(58,9,'评论列表','UserComment/index','icon-comments-alt',1426580628,1426580628,4,1),
	(59,58,'添加','UserComment/add','',1426580628,1426580628,1,1),
	(60,58,'编辑','UserComment/edit','',1426580628,1426580628,2,1),
	(61,58,'设置状态','UserComment/setStatus','',1426580628,1426580628,3,1),
	(62,9,'回收站','Document/recycle','icon-trash',1427475588,1430290597,5,1),
	(63,10,'上传管理','Upload/index','',1427475588,1427475588,1,1),
	(64,63,'上传文件','Upload/upload','',1427475588,1427475588,1,1),
	(65,63,'下载图片','Upload/downremoteimg','',1427475588,1427475588,2,1),
	(66,63,'文件浏览','Upload/fileManager','',1427475588,1427475588,3,1),
	(67,11,'用户列表','User/index','icon-user',1426580628,1426580628,1,1),
	(68,67,'添加','User/add','',1426580628,1426580628,1,1),
	(69,67,'编辑','User/edit','',1426580628,1426580628,2,1),
	(70,67,'设置状态','User/setStatus','',1426580628,1426580628,3,1),
	(71,11,'部门管理','UserGroup/index','icon-sitemap',1426580628,1426580628,2,1),
	(72,71,'添加','UserGroup/add','',1426580628,1426580628,1,1),
	(73,71,'编辑','UserGroup/edit','',1426580628,1426580628,2,1),
	(74,71,'设置状态','UserGroup/setStatus','',1426580628,1426580628,3,1);

/*!40000 ALTER TABLE `ct_system_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ct_tag
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_tag`;

CREATE TABLE `ct_tag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(32) NOT NULL COMMENT '标签',
  `count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '数量',
  `group` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '分组',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `utime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='标签表';



# Dump of table ct_upload
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_upload`;

CREATE TABLE `ct_upload` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '上传ID',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件链接',
  `ext` char(4) NOT NULL DEFAULT '' COMMENT '文件类型',
  `size` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件sha1编码',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `utime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件上传表';



# Dump of table ct_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_user`;

CREATE TABLE `ct_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名或昵称',
  `email` varchar(32) NOT NULL DEFAULT '' COMMENT '用户邮箱',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '用户密码',
  `group` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '部门/用户组ID',
  `avatar` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户头像',
  `score` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户积分',
  `money` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '账户余额',
  `sex` enum('-1','0','1') NOT NULL DEFAULT '0' COMMENT '用户性别',
  `age` int(4) NOT NULL DEFAULT '0' COMMENT '年龄',
  `birthday` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '生日',
  `summary` varchar(127) NOT NULL DEFAULT '' COMMENT '心情',
  `extend` varchar(1024) NOT NULL DEFAULT '' COMMENT '用户信息扩展',
  `login` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `last_login_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最近登陆时间',
  `last_login_ip` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最近登陆IP',
  `reg_ip` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '注册IP',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `utime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `mobile` (`mobile`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户会员信息表';

LOCK TABLES `ct_user` WRITE;
/*!40000 ALTER TABLE `ct_user` DISABLE KEYS */;

INSERT INTO `ct_user` (`id`, `username`, `email`, `mobile`, `password`, `group`, `avatar`, `score`, `money`, `sex`, `age`, `birthday`, `summary`, `extend`, `login`, `last_login_time`, `last_login_ip`, `reg_ip`, `ctime`, `utime`, `sort`, `status`)
VALUES
	(1,'admin','598821125@qq.com','15005173785','79cc780bd21b161230268824080b8476',1,0,0,0,'0',0,0,'','',7,1432362010,2130706433,0,0,0,0,1);

/*!40000 ALTER TABLE `ct_user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ct_user_comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_user_comment`;

CREATE TABLE `ct_user_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '评论ID',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '评论父ID',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `doc_id` int(11) unsigned NOT NULL COMMENT '评论文档ID',
  `content` text COMMENT '评论内容',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `utime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `ip` varchar(15) NOT NULL COMMENT '来源IP',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论表';



# Dump of table ct_user_digg
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_user_digg`;

CREATE TABLE `ct_user_digg` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `doc_id` int(11) unsigned NOT NULL COMMENT '文档ID',
  `good` text COMMENT '赞',
  `bad` text COMMENT '踩',
  `mark` text COMMENT '收藏',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Digg表';



# Dump of table ct_user_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_user_group`;

CREATE TABLE `ct_user_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '部门ID',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上级部门ID',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '部门名称',
  `icon` varchar(32) NOT NULL COMMENT '图标',
  `auth` varchar(1024) NOT NULL DEFAULT '' COMMENT '权限',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `utime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='部门信息表';

LOCK TABLES `ct_user_group` WRITE;
/*!40000 ALTER TABLE `ct_user_group` DISABLE KEYS */;

INSERT INTO `ct_user_group` (`id`, `pid`, `title`, `icon`, `auth`, `ctime`, `utime`, `sort`, `status`)
VALUES
	(1,0,'管理员','','',1426881003,1427552428,0,1);

/*!40000 ALTER TABLE `ct_user_group` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ct_user_message
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ct_user_message`;

CREATE TABLE `ct_user_message` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '消息ID',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '消息父ID',
  `title` varchar(1024) NOT NULL DEFAULT '' COMMENT '消息内容',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0系统消息,1评论消息,2私信消息',
  `to_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '接收用户ID',
  `from_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '私信消息发信用户ID',
  `is_read` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发送时间',
  `utime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户消息表';

