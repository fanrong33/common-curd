
CREATE TABLE `t_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '',
  `is_deleted` tinyint(1) DEFAULT '0',
  `update_time` int(10) DEFAULT '0',
  `create_time` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `t_group` */

insert  into `t_group`(`id`,`name`,`is_deleted`,`update_time`,`create_time`) values (1,'工具',0,1476431191,1476430011),(2,'游戏',0,1476431207,1476431198);

/*Table structure for table `t_page` */

CREATE TABLE `t_page` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT '0',
  `name` varchar(50) DEFAULT '',
  `url` varchar(255) DEFAULT '',
  `is_deleted` tinyint(1) DEFAULT '0',
  `update_time` int(10) DEFAULT '0',
  `create_time` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `t_page` */

insert  into `t_page`(`id`,`group_id`,`name`,`url`,`is_deleted`,`update_time`,`create_time`) values (1,2,'百度','http://www.baidu.com',0,1476455368,1476432952),(2,2,'Facebook','http://www.facebook.com',0,1476455398,1476455357),(3,1,'谷歌','http://www.google.com',0,1476455383,1476455383);
