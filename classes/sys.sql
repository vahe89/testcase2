

CREATE TABLE IF NOT EXISTS `sys_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `val` text NOT NULL,
  `lang` varchar(4) NOT NULL DEFAULT 'en',
  `lid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Дамп данных таблицы mxa.sys_config: 1 rows
/*!40000 ALTER TABLE `sys_config` DISABLE KEYS */;
INSERT IGNORE INTO `sys_config` (`id`, `name`, `val`, `lang`, `lid`) VALUES
	(1, 'locallogin_rclog', '123456', 'en', 0),
	(2, 'artp_data', '', 'en', 0);
/*!40000 ALTER TABLE `sys_config` ENABLE KEYS */;


CREATE TABLE IF NOT EXISTS `sys_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tbl` varchar(255) NOT NULL DEFAULT '',
  `tid` int(11) unsigned NOT NULL DEFAULT '0',
  `title` text NOT NULL,
  `full` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `ext` varchar(255) NOT NULL DEFAULT '',
  `mime` varchar(255) NOT NULL DEFAULT '',
  `ftype` varchar(255) NOT NULL DEFAULT '',
  `dir` varchar(255) NOT NULL DEFAULT '',
  `isnew` int(11) unsigned NOT NULL DEFAULT '0',
  `lang` varchar(3) NOT NULL DEFAULT 'en',
  `lid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `sys_links` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `tbl` varchar(255) NOT NULL DEFAULT '',
  `tid` int(11) unsigned NOT NULL DEFAULT '0',
  `type` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '0 - url (url F), 1 - resource (rtbl && rid Fs)',
  `url` varchar(1024) NOT NULL DEFAULT '',
  `object` varchar(255) NOT NULL DEFAULT '',
  `rtbl` varchar(255) NOT NULL DEFAULT '',
  `rid` int(11) unsigned NOT NULL DEFAULT '0',
  `slug` varchar(50) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `linkn` int(11) unsigned NOT NULL DEFAULT '0',
  `target` varchar(255) NOT NULL DEFAULT '',
  `lang` varchar(3) NOT NULL DEFAULT 'en',
  `lid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `rid` (`rid`),
  KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `sys_m2m` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rel_name` varchar(255) NOT NULL DEFAULT '',
  `mtbl` varchar(255) NOT NULL DEFAULT '',
  `mid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `stbl` varchar(255) NOT NULL DEFAULT '',
  `sid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_tbl` varchar(255) NOT NULL DEFAULT '',
  `meta_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `sid` (`sid`),
  KEY `rel_name` (`rel_name`),
  KEY `meta_id` (`meta_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `sys_prios` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tbl` varchar(255) DEFAULT NULL,
  `tid` int(11) unsigned NOT NULL DEFAULT '0',
  `prio` int(11) unsigned NOT NULL DEFAULT '0',
  `parent` int(11) unsigned NOT NULL DEFAULT '0',
  `group` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `sys_sf_rest_api_dirty_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `obj` varchar(50) NOT NULL DEFAULT '',
  `obj_id` varchar(50) NOT NULL DEFAULT '',
  `act` varchar(50) NOT NULL DEFAULT '',
  `dirty_from` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sf_error` varchar(1024) NOT NULL DEFAULT '',
  `retries` int(11) NOT NULL DEFAULT '0',
  `lang` varchar(20) NOT NULL DEFAULT 'en',
  `lid` bigint(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj` (`obj`,`obj_id`,`act`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `sys_users_access` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `lang` varchar(255) NOT NULL DEFAULT 'en',
  `lid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `sys_access_track` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`user_id` BIGINT(20) NOT NULL DEFAULT '0',
	`username` VARCHAR(255) NOT NULL DEFAULT '',
	`domain` VARCHAR(255) NOT NULL DEFAULT '',
	`url` VARCHAR(255) NOT NULL DEFAULT '',
	`slug_req` VARCHAR(255) NOT NULL DEFAULT '',
	`app` VARCHAR(50) NOT NULL DEFAULT '',
	`time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	INDEX `user_id` (`user_id`),
	FULLTEXT INDEX `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sys_change_history` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`user_id` BIGINT(20) NOT NULL DEFAULT '0',
	`username` VARCHAR(255) NOT NULL DEFAULT '',
	`obj` VARCHAR(255) NOT NULL DEFAULT '',
	`obj_id` BIGINT(20) NOT NULL DEFAULT '0',
	`obj_slug` VARCHAR(255) NOT NULL DEFAULT '',
	`obj_sf_id` VARCHAR(255) NOT NULL DEFAULT '',
	`act` VARCHAR(255) NOT NULL DEFAULT '',
	`upd_fields` VARCHAR(255) NOT NULL DEFAULT '',
	`old` LONGTEXT NOT NULL,
	`new` LONGTEXT NOT NULL,
	`app` VARCHAR(50) NOT NULL DEFAULT '',
	`time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	INDEX `obj_id` (`obj_id`),
	INDEX `user_id` (`user_id`),
	FULLTEXT INDEX `username` (`username`),
	FULLTEXT INDEX `obj` (`obj`),
	FULLTEXT INDEX `obj_slug` (`obj_slug`),
	FULLTEXT INDEX `obj_sf_id` (`obj_sf_id`),
	FULLTEXT INDEX `field` (`upd_fields`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rc_calls` (
	`id` BIGINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`call_time` DATETIME NULL DEFAULT NULL,
	`lead_sf_id` VARCHAR(100) NOT NULL DEFAULT '',
	`sr_sf_id` VARCHAR(100) NOT NULL DEFAULT '',
	`sr_phone` VARCHAR(100) NOT NULL DEFAULT '',
	`lead_phone` VARCHAR(100) NOT NULL DEFAULT '',
	`is_inbound` TINYINT(1) NOT NULL DEFAULT '0',
	`duration` BIGINT(20) NOT NULL DEFAULT '0',
	`details` TEXT NOT NULL,
	`ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`lang` VARCHAR(4) NOT NULL DEFAULT 'en',
	`lid` BIGINT(11) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX `lead_sf_id` (`lead_sf_id`),
	INDEX `sr_sf_id` (`sr_sf_id`),
	INDEX `sr_phone` (`sr_phone`),
	INDEX `lead_phone` (`lead_phone`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DYNAMIC
;

