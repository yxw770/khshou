/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 5.5.53 : Database - ytpays
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `1106111321_admin` */

DROP TABLE IF EXISTS `1106111321_admin`;

CREATE TABLE `1106111321_admin` (
  `adminid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `utype` tinyint(1) DEFAULT '0',
  `adminname` varchar(10) DEFAULT NULL,
  `adminpass` char(64) DEFAULT NULL,
  `realname` varchar(10) DEFAULT NULL,
  `ptype` int(1) DEFAULT '0',
  `account` varchar(20) DEFAULT NULL,
  `is_safe` tinyint(1) DEFAULT '0',
  `safekey` char(6) DEFAULT NULL,
  `is_whiteip` tinyint(1) DEFAULT '0',
  `whiteip` varchar(100) DEFAULT NULL,
  `viewlimit` varchar(250) DEFAULT NULL,
  `is_state` tinyint(1) DEFAULT '1',
  `addtime` datetime DEFAULT NULL,
  `is_trush` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`adminid`),
  UNIQUE KEY `adminid` (`adminname`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_admin` */

insert  into `1106111321_admin`(`adminid`,`utype`,`adminname`,`adminpass`,`realname`,`ptype`,`account`,`is_safe`,`safekey`,`is_whiteip`,`whiteip`,`viewlimit`,`is_state`,`addtime`,`is_trush`) values (1,1,'admin','$2y$10$4YAZD0JxwJTZ78Freic4.OjjFp8TMSrxOJBmAaeRiLG6iyNsfIf4G','',0,NULL,0,'',0,'','Accessinfo|Arccat|Arclist|Busanalysis|Channel|Channelanalysis|Goodslist|Index|Log|Orderlist|Psd|Set|Settlement|DlSettlement|Settlementlog|Settlementsum|Timedata|Tousu|Ulist|Userlist|Userloglist|Userpaylist|Withdraw',1,NULL,0);

/*Table structure for table `1106111321_adminarticlecate` */

DROP TABLE IF EXISTS `1106111321_adminarticlecate`;

CREATE TABLE `1106111321_adminarticlecate` (
  `articlecateid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catename` varchar(20) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `is_lock` int(1) DEFAULT '0',
  `is_trush` int(1) DEFAULT '0',
  PRIMARY KEY (`articlecateid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_adminarticlecate` */

insert  into `1106111321_adminarticlecate`(`articlecateid`,`catename`,`addtime`,`is_lock`,`is_trush`) values (1,'后台通知','2018-12-18 08:55:41',0,0),(2,'首页公告','2018-12-18 13:44:13',0,0),(3,'测试','2018-12-26 16:05:55',0,1),(4,'1','2018-12-26 16:08:08',0,1),(5,'1','2018-12-26 16:09:50',0,1),(6,'1','2018-12-26 16:14:52',0,1),(7,'154546545641','2018-12-28 18:36:34',0,1),(8,'154546545641','2018-12-28 18:36:34',0,1);

/*Table structure for table `1106111321_adminarticlelist` */

DROP TABLE IF EXISTS `1106111321_adminarticlelist`;

CREATE TABLE `1106111321_adminarticlelist` (
  `articlelistid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `articlecateid` int(10) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `catename` varchar(20) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `content` text,
  `addtime` datetime DEFAULT NULL,
  `is_noticed` int(1) DEFAULT '0',
  `is_trush` int(1) DEFAULT '0',
  PRIMARY KEY (`articlelistid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_adminarticlelist` */

/*Table structure for table `1106111321_admincheck` */

DROP TABLE IF EXISTS `1106111321_admincheck`;

CREATE TABLE `1106111321_admincheck` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adminid` int(10) DEFAULT NULL,
  `adminname` varchar(20) DEFAULT NULL,
  `sessionid` varchar(100) DEFAULT NULL,
  `adminagent` char(200) DEFAULT NULL,
  `admintoken` char(64) DEFAULT NULL,
  `adminuniqid` varchar(20) DEFAULT NULL,
  `passkey` varchar(64) DEFAULT NULL,
  `passkey_part` varchar(64) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `login_ip` char(15) DEFAULT NULL,
  `admincard` char(64) DEFAULT NULL,
  `adminleave` datetime DEFAULT NULL,
  `adminsafekey` varchar(64) DEFAULT NULL,
  `is_safekey` tinyint(1) DEFAULT '0',
  `is_state` tinyint(1) DEFAULT '1',
  `err_count` tinyint(1) DEFAULT '0',
  `err_time` datetime DEFAULT NULL,
  `mac` char(17) DEFAULT NULL,
  `privilege` varchar(255) DEFAULT NULL,
  `id_weixin` tinyint(1) DEFAULT '0',
  `openid` varchar(50) DEFAULT NULL,
  `weixin_addtime` datetime DEFAULT NULL,
  `weixin_lock` tinyint(1) DEFAULT '0',
  `weixin_updatetime` datetime DEFAULT NULL,
  `weixin_check` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_admincheck` */

insert  into `1106111321_admincheck`(`id`,`adminid`,`adminname`,`sessionid`,`adminagent`,`admintoken`,`adminuniqid`,`passkey`,`passkey_part`,`login_time`,`login_ip`,`admincard`,`adminleave`,`adminsafekey`,`is_safekey`,`is_state`,`err_count`,`err_time`,`mac`,`privilege`,`id_weixin`,`openid`,`weixin_addtime`,`weixin_lock`,`weixin_updatetime`,`weixin_check`) values (1,1,'admin','0','0','0','0','0',NULL,'0000-00-00 00:00:00','0',NULL,'0000-00-00 00:00:00',NULL,0,1,0,NULL,NULL,NULL,0,NULL,NULL,0,NULL,0);

/*Table structure for table `1106111321_adminconfig` */

DROP TABLE IF EXISTS `1106111321_adminconfig`;

CREATE TABLE `1106111321_adminconfig` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stmp` varchar(50) DEFAULT NULL,
  `stmpeemail` varchar(50) DEFAULT NULL,
  `sttmppwd` varchar(50) DEFAULT NULL,
  `qttheme` varchar(20) DEFAULT NULL,
  `usertheme` varchar(20) DEFAULT NULL,
  `paytheme` varchar(20) DEFAULT NULL,
  `is_register` int(1) DEFAULT '1',
  `is_verify` int(1) DEFAULT '0',
  `takecashlowest` decimal(6,2) DEFAULT NULL,
  `takecashfrom` smallint(2) DEFAULT NULL,
  `takecashto` smallint(2) DEFAULT NULL,
  `is_takecashstate` int(1) DEFAULT '1',
  `is_sitestate` int(1) DEFAULT '1',
  `closesitemsg` varchar(200) DEFAULT NULL,
  `reporttimes` int(2) DEFAULT NULL,
  `takecashtime` int(2) DEFAULT NULL,
  `fee` decimal(6,2) DEFAULT NULL,
  `feemost` decimal(6,2) DEFAULT NULL,
  `is_dwz` int(1) DEFAULT '1',
  `dwz` varchar(20) DEFAULT NULL,
  `is_license` int(1) DEFAULT '0',
  `is_sms` int(1) DEFAULT '0',
  `is_phone` int(1) DEFAULT '0',
  `is_weixin` int(1) DEFAULT '0',
  `is_trush` int(1) DEFAULT '0',
  `is_filterkey` int(1) DEFAULT '0',
  `filterkey` varchar(500) DEFAULT '',
  `is_random` int(1) DEFAULT '0',
  `wx_appid` varchar(20) DEFAULT '',
  `wx_secret` varchar(50) DEFAULT '',
  `is_wx` int(1) DEFAULT '0',
  `is_sms_userpay` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_adminconfig` */

insert  into `1106111321_adminconfig`(`id`,`stmp`,`stmpeemail`,`sttmppwd`,`qttheme`,`usertheme`,`paytheme`,`is_register`,`is_verify`,`takecashlowest`,`takecashfrom`,`takecashto`,`is_takecashstate`,`is_sitestate`,`closesitemsg`,`reporttimes`,`takecashtime`,`fee`,`feemost`,`is_dwz`,`dwz`,`is_license`,`is_sms`,`is_phone`,`is_weixin`,`is_trush`,`is_filterkey`,`filterkey`,`is_random`,`wx_appid`,`wx_secret`,`is_wx`,`is_sms_userpay`) values (1,'smtpdm.aliyun.com','','','flat','flat','default',1,0,'50.00',0,23,1,1,'升级中，请稍等。',3,1,'1.00','50.00',0,'suoim',1,1,0,0,0,1,'辅助|外挂|',0,'','',0,2);

/*Table structure for table `1106111321_adminlog` */

DROP TABLE IF EXISTS `1106111321_adminlog`;

CREATE TABLE `1106111321_adminlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adminid` int(10) DEFAULT NULL,
  `adminname` varchar(20) DEFAULT NULL,
  `loginip` varchar(15) DEFAULT NULL,
  `logintime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `adminid` (`adminid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_adminlog` */

/*Table structure for table `1106111321_adminpaychannel` */

DROP TABLE IF EXISTS `1106111321_adminpaychannel`;

CREATE TABLE `1106111321_adminpaychannel` (
  `channelid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `providerid` int(10) DEFAULT NULL,
  `payway` varchar(20) DEFAULT NULL,
  `channelname` varchar(20) DEFAULT NULL,
  `ptproportion` decimal(6,4) DEFAULT '0.0000',
  `userproportion` decimal(6,4) DEFAULT '0.0000',
  `is_state` tinyint(1) DEFAULT '0',
  `is_random` int(1) DEFAULT '0',
  `is_trush` int(1) DEFAULT '0',
  PRIMARY KEY (`channelid`),
  KEY `channelid` (`channelid`,`payway`,`is_state`),
  KEY `payway` (`payway`,`is_state`,`is_trush`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_adminpaychannel` */

insert  into `1106111321_adminpaychannel`(`channelid`,`providerid`,`payway`,`channelname`,`ptproportion`,`userproportion`,`is_state`,`is_random`,`is_trush`) values (1,1,'alipay','支付宝扫码','0.0240','0.9700',1,1,0),(2,1,'alipaywap','支付宝wap','0.0240','0.9700',1,1,0),(3,1,'weixin','微信扫码','0.0220','0.9700',1,1,0),(4,1,'weixinwap','微信扫码wap','0.0220','0.9700',1,1,0),(5,1,'qqrcode','QQ扫码','0.0400','0.9900',1,1,0),(6,1,'qqwap','QQwap','0.0400','0.9900',1,0,0),(7,1,'tenpay','财付通','0.0140','0.9800',0,0,0),(8,1,'weixingzh','微信公众号','0.0220','0.9700',1,0,0),(9,1,'jdsm','京东扫码','0.0140','0.9800',0,0,0),(10,1,'wxgzh','微信公众号','0.0240','0.9700',0,0,0);

/*Table structure for table `1106111321_adminpayprovider` */

DROP TABLE IF EXISTS `1106111321_adminpayprovider`;

CREATE TABLE `1106111321_adminpayprovider` (
  `providerid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `providername` varchar(20) DEFAULT NULL,
  `directory` varchar(20) DEFAULT NULL,
  `apiurl` varchar(50) DEFAULT NULL,
  `accountid` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `payway` varchar(20) DEFAULT NULL,
  `is_state` tinyint(1) DEFAULT '0',
  `is_random` tinyint(1) DEFAULT '0',
  `orther1` varchar(50) DEFAULT NULL,
  `orther2` varchar(50) DEFAULT NULL,
  `is_trush` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`providerid`),
  KEY `providerid` (`providerid`,`is_state`,`is_trush`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_adminpayprovider` */

/*Table structure for table `1106111321_buylist` */

DROP TABLE IF EXISTS `1106111321_buylist`;

CREATE TABLE `1106111321_buylist` (
  `buylistid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `orderlistid` int(10) DEFAULT NULL,
  `goodlistid` int(10) DEFAULT NULL,
  `channelid` int(10) DEFAULT NULL,
  `providerid` int(10) DEFAULT NULL,
  `productid` int(10) DEFAULT NULL,
  `defineid` int(10) DEFAULT NULL,
  `freezeid` int(10) DEFAULT NULL,
  `orderid` varchar(20) DEFAULT NULL,
  `ordermoney` decimal(18,2) DEFAULT '0.00',
  `price` decimal(18,2) DEFAULT '0.00',
  `cost` decimal(18,2) DEFAULT '0.00',
  `quantity` int(10) DEFAULT NULL,
  `paymoney` decimal(18,2) DEFAULT '0.00',
  `userproportion` decimal(6,4) DEFAULT '0.0000',
  `ptproportion` decimal(6,4) DEFAULT '0.0000',
  `usergain` decimal(18,2) DEFAULT '0.00',
  `ptgain` decimal(18,2) DEFAULT '0.00',
  `channelname` varchar(20) DEFAULT NULL,
  `is_frozen` int(1) DEFAULT '0',
  `is_unset` int(1) DEFAULT '0',
  `is_payment` int(1) DEFAULT '0',
  `is_buylist` int(1) DEFAULT '1',
  `is_product` int(1) DEFAULT '0',
  `updatetime` datetime DEFAULT NULL,
  `search_tips` int(10) DEFAULT '0',
  PRIMARY KEY (`buylistid`),
  KEY `userid` (`userid`),
  KEY `userid_2` (`userid`,`is_frozen`,`is_unset`,`is_payment`,`is_buylist`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_buylist` */

/*Table structure for table `1106111321_email` */

DROP TABLE IF EXISTS `1106111321_email`;

CREATE TABLE `1106111321_email` (
  `emailid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `from` varchar(11) DEFAULT NULL,
  `to` varchar(11) DEFAULT NULL,
  `content` varchar(70) DEFAULT NULL,
  `discontent` varchar(20) DEFAULT NULL,
  `goodlistid` int(10) DEFAULT NULL,
  `is_send` int(1) DEFAULT '0',
  `is_state` int(1) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  PRIMARY KEY (`emailid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_email` */

/*Table structure for table `1106111321_freeze` */

DROP TABLE IF EXISTS `1106111321_freeze`;

CREATE TABLE `1106111321_freeze` (
  `freezeid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `orderlistid` int(10) DEFAULT NULL,
  `buylistid` int(10) DEFAULT NULL,
  `goodlistid` int(10) DEFAULT NULL,
  `channelid` int(10) DEFAULT NULL,
  `providerid` int(10) DEFAULT NULL,
  `productid` int(10) DEFAULT NULL,
  `defineid` int(10) DEFAULT NULL,
  `orderid` varchar(20) DEFAULT NULL,
  `ordermoney` decimal(18,2) DEFAULT NULL,
  `price` decimal(18,2) DEFAULT NULL,
  `cost` decimal(18,2) DEFAULT NULL,
  `quantity` int(10) DEFAULT NULL,
  `paymoney` decimal(18,2) DEFAULT NULL,
  `userrate` decimal(6,4) DEFAULT NULL,
  `ptrate` decimal(6,4) DEFAULT NULL,
  `usergain` decimal(18,2) DEFAULT NULL,
  `ptgain` decimal(18,2) DEFAULT NULL,
  `channelname` varchar(20) DEFAULT NULL,
  `is_frozen` int(1) DEFAULT '1',
  `is_payment` int(1) DEFAULT '0',
  `updatetime` datetime DEFAULT NULL,
  PRIMARY KEY (`freezeid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_freeze` */

/*Table structure for table `1106111321_guestconfig` */

DROP TABLE IF EXISTS `1106111321_guestconfig`;

CREATE TABLE `1106111321_guestconfig` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sitename` varchar(50) DEFAULT NULL,
  `sitetile` varchar(50) DEFAULT NULL,
  `siteurl` varchar(50) DEFAULT NULL,
  `listurl` varchar(50) DEFAULT NULL,
  `detailurl` varchar(50) DEFAULT NULL,
  `linkurl` varchar(50) DEFAULT NULL,
  `sitekeyword` varchar(100) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `qq` varchar(11) DEFAULT NULL,
  `tel` varchar(13) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `copyright` varchar(100) DEFAULT NULL,
  `bdtuisong` varchar(800) DEFAULT NULL,
  `icp` varchar(50) DEFAULT NULL,
  `baidu_verification` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_guestconfig` */

insert  into `1106111321_guestconfig`(`id`,`sitename`,`sitetile`,`siteurl`,`listurl`,`detailurl`,`linkurl`,`sitekeyword`,`description`,`qq`,`tel`,`company`,`address`,`email`,`copyright`,`bdtuisong`,`icp`,`baidu_verification`) values (1,'发卡平台','发卡平台www.baidu.com','www.baidu.com','www.baidu.com','www.baidu.com','www.baidu.com','发卡网，自动发卡平台，发卡平台，自动发卡，自动发卡平台，发卡平台，发卡网','发卡平台是一家专业的互联网运营的在线销售、发货和充值自动完成的新一代虚拟商品在线交易平台','1106111321','0712-12345678','网络科技有限公司','徐州市云龙区复兴南路186号','admin@163.com','© Copyright © 2018-2019  发卡平台|网络科技有限公司（www.baidu.com） All rights reserved.','','苏ICP备18068308号-1','');

/*Table structure for table `1106111321_orderdefine` */

DROP TABLE IF EXISTS `1106111321_orderdefine`;

CREATE TABLE `1106111321_orderdefine` (
  `defineid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderlistid` int(10) DEFAULT NULL,
  `buylistid` int(10) DEFAULT NULL,
  `qq` varchar(11) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `is_email` int(1) DEFAULT '0',
  `contact` varchar(20) DEFAULT NULL,
  `ip` char(15) DEFAULT NULL,
  `tel` char(11) DEFAULT NULL,
  `is_tel` int(1) DEFAULT '0',
  `is_coupon` int(1) DEFAULT '0',
  `couponcode` char(20) DEFAULT NULL,
  `amount` decimal(7,2) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `is_pwd` int(1) DEFAULT '0',
  `orderpwd` varchar(20) DEFAULT NULL,
  `payway` varchar(20) DEFAULT NULL,
  `is_trush` int(1) DEFAULT '0',
  PRIMARY KEY (`defineid`),
  KEY `orderid` (`orderlistid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_orderdefine` */

/*Table structure for table `1106111321_orderlist` */

DROP TABLE IF EXISTS `1106111321_orderlist`;

CREATE TABLE `1106111321_orderlist` (
  `orderlistid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `goodlistid` int(10) DEFAULT NULL,
  `buylistid` int(10) DEFAULT NULL,
  `channelid` int(10) DEFAULT NULL,
  `providerid` int(10) DEFAULT NULL,
  `defineid` int(10) DEFAULT NULL,
  `productid` int(10) DEFAULT NULL,
  `orderid` varchar(20) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `payway` varchar(20) DEFAULT NULL,
  `ordermoney` decimal(18,2) DEFAULT '0.00',
  `price` decimal(18,2) DEFAULT '0.00',
  `quantity` int(10) DEFAULT NULL,
  `paymoney` decimal(18,2) DEFAULT '0.00',
  `userproportion` decimal(6,4) DEFAULT '0.0000',
  `ptproportion` decimal(6,4) DEFAULT '0.0000',
  `userqq` varchar(11) DEFAULT NULL,
  `channelname` varchar(20) DEFAULT NULL,
  `is_payment` int(1) DEFAULT '0',
  `is_frozen` int(1) DEFAULT '0',
  `is_buylist` int(1) DEFAULT '0',
  `is_product` int(1) DEFAULT '0',
  `is_selllist` int(1) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  `addtime_int` int(11) DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `search_tips` int(10) DEFAULT '0',
  `is_trush` int(1) DEFAULT '0',
  `is_pwd` int(1) DEFAULT '0',
  `orderpwd` varchar(20) DEFAULT NULL,
  `is_state` int(1) DEFAULT '1',
  `is_sub` int(1) unsigned NOT NULL DEFAULT '0',
  `sub_userid` int(10) unsigned NOT NULL DEFAULT '0',
  `sub_money` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `is_sub_ship` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderlistid`),
  UNIQUE KEY `orderid` (`orderid`),
  KEY `userid` (`userid`,`is_frozen`,`is_payment`,`is_buylist`,`is_selllist`),
  KEY `orderquery1` (`orderid`),
  KEY `orderquery2` (`contact`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_orderlist` */

/*Table structure for table `1106111321_retpwd` */

DROP TABLE IF EXISTS `1106111321_retpwd`;

CREATE TABLE `1106111321_retpwd` (
  `retpwdid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `token` varchar(32) DEFAULT NULL,
  `is_state` int(1) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  PRIMARY KEY (`retpwdid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_retpwd` */

/*Table structure for table `1106111321_selllist` */

DROP TABLE IF EXISTS `1106111321_selllist`;

CREATE TABLE `1106111321_selllist` (
  `selllistid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `goodlistid` int(10) DEFAULT NULL,
  `orderlistid` int(10) DEFAULT NULL,
  `productid` int(10) DEFAULT NULL,
  `orderid` varchar(20) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `is_trush` int(1) DEFAULT '0',
  PRIMARY KEY (`selllistid`),
  UNIQUE KEY `productid` (`productid`),
  KEY `goodlistid` (`goodlistid`,`productid`),
  KEY `order` (`orderid`,`productid`),
  KEY `userid` (`userid`,`productid`,`addtime`),
  KEY `orderlistid` (`orderlistid`,`is_trush`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_selllist` */

/*Table structure for table `1106111321_sms` */

DROP TABLE IF EXISTS `1106111321_sms`;

CREATE TABLE `1106111321_sms` (
  `smsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `tel` varchar(11) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  `content` varchar(70) DEFAULT NULL,
  `is_send` int(1) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `type` int(1) DEFAULT '0',
  PRIMARY KEY (`smsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_sms` */

/*Table structure for table `1106111321_sms_wechat` */

DROP TABLE IF EXISTS `1106111321_sms_wechat`;

CREATE TABLE `1106111321_sms_wechat` (
  `smsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  `content` varchar(70) DEFAULT NULL,
  `is_send` int(1) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `type` int(1) DEFAULT '0',
  `typename` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`smsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_sms_wechat` */

/*Table structure for table `1106111321_user` */

DROP TABLE IF EXISTS `1106111321_user`;

CREATE TABLE `1106111321_user` (
  `userid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) DEFAULT NULL,
  `password` char(64) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `register_ip` varchar(15) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `tel` char(11) DEFAULT NULL,
  `sign` tinyint(1) DEFAULT '0',
  `flag` int(1) DEFAULT '0',
  `remark` varchar(100) DEFAULT NULL,
  `is_vip` int(1) DEFAULT '0',
  `is_state` int(1) DEFAULT '1',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_user` */

/*Table structure for table `1106111321_user_wechat` */

DROP TABLE IF EXISTS `1106111321_user_wechat`;

CREATE TABLE `1106111321_user_wechat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(128) NOT NULL,
  `userid` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `nickname` varchar(64) DEFAULT NULL,
  `avatar` varchar(256) DEFAULT NULL,
  `union_id` varchar(128) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_wx_msg` int(1) DEFAULT '0',
  `is_login` int(1) DEFAULT '0',
  `is_complain` int(1) DEFAULT '0',
  `is_kucun` int(1) DEFAULT '0',
  `is_payment` int(1) DEFAULT '0',
  `is_buylist` int(1) DEFAULT '0',
  `is_tongji` int(1) DEFAULT '0',
  `is_wx` int(1) DEFAULT '0',
  `is_notice` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_user_wechat` */

/*Table structure for table `1106111321_user_wechat_token` */

DROP TABLE IF EXISTS `1106111321_user_wechat_token`;

CREATE TABLE `1106111321_user_wechat_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(50) NOT NULL DEFAULT '',
  `userid` int(11) NOT NULL DEFAULT '0',
  `username` varchar(20) DEFAULT '',
  `token` varchar(50) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_used` int(1) DEFAULT '0',
  `is_scan` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_user_wechat_token` */

/*Table structure for table `1106111321_usercheck` */

DROP TABLE IF EXISTS `1106111321_usercheck`;

CREATE TABLE `1106111321_usercheck` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  `sessionid` varchar(100) DEFAULT NULL,
  `useragent` char(200) DEFAULT NULL,
  `usertoken` char(64) DEFAULT NULL,
  `useruniqid` varchar(20) DEFAULT NULL,
  `passkey` varchar(64) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `login_ip` char(15) DEFAULT NULL,
  `usercard` char(64) DEFAULT NULL,
  `userleave` datetime DEFAULT NULL,
  `usersafekey` varchar(64) DEFAULT NULL,
  `is_safekey` tinyint(1) DEFAULT '0',
  `is_state` tinyint(1) DEFAULT '1',
  `is_audit` tinyint(1) DEFAULT '0',
  `err_count` tinyint(1) DEFAULT '0',
  `err_time` datetime DEFAULT NULL,
  `usercard2` char(64) DEFAULT NULL,
  `userleave2` datetime DEFAULT NULL,
  `id_weixin` tinyint(1) DEFAULT '0',
  `openid` varchar(50) DEFAULT NULL,
  `weixin_addtime` datetime DEFAULT NULL,
  `weixin_lock` tinyint(1) DEFAULT '0',
  `weixin_updatetime` datetime DEFAULT NULL,
  `weixin_check` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_usercheck` */

/*Table structure for table `1106111321_usercoupon` */

DROP TABLE IF EXISTS `1106111321_usercoupon`;

CREATE TABLE `1106111321_usercoupon` (
  `couponid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `goodlistid` int(10) DEFAULT NULL,
  `is_all` int(1) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `validity` int(2) DEFAULT NULL,
  `gptime` datetime DEFAULT NULL,
  `amount` decimal(5,2) DEFAULT NULL,
  `type` int(1) DEFAULT '1',
  `remark` varchar(255) DEFAULT NULL,
  `couponcode` varchar(16) DEFAULT NULL,
  `is_state` int(1) DEFAULT '0',
  PRIMARY KEY (`couponid`),
  KEY `userid` (`userid`,`couponcode`,`is_state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_usercoupon` */

/*Table structure for table `1106111321_userdefine` */

DROP TABLE IF EXISTS `1106111321_userdefine`;

CREATE TABLE `1106111321_userdefine` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `qq` varchar(11) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `siteurl` varchar(50) DEFAULT NULL,
  `sitename` varchar(50) DEFAULT NULL,
  `kucun_show` tinyint(1) DEFAULT '0',
  `tel` char(11) DEFAULT NULL,
  `contact_limit` tinyint(1) DEFAULT '0',
  `usertheme` varchar(30) DEFAULT NULL,
  `paytheme` varchar(20) DEFAULT NULL,
  `usernotice` varchar(255) DEFAULT NULL,
  `define` varchar(20) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  `linkid` varchar(20) DEFAULT NULL,
  `unset_articlelist` int(10) DEFAULT NULL,
  `link_state` int(1) DEFAULT '1',
  `check_goods` int(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `linkid` (`linkid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_userdefine` */

/*Table structure for table `1106111321_usergoodcate` */

DROP TABLE IF EXISTS `1106111321_usergoodcate`;

CREATE TABLE `1106111321_usergoodcate` (
  `goodcateid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `catename` varchar(20) DEFAULT NULL,
  `linkid` char(16) DEFAULT NULL,
  `sortid` int(10) DEFAULT '100',
  `define` varchar(20) DEFAULT NULL,
  `paytheme` varchar(20) DEFAULT NULL,
  `siteurl` varchar(50) DEFAULT NULL,
  `sitename` varchar(50) DEFAULT NULL,
  `qq` char(11) DEFAULT NULL,
  `is_state` int(1) DEFAULT '1',
  `status` int(1) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  `is_trush` int(1) DEFAULT '0',
  PRIMARY KEY (`goodcateid`),
  KEY `linkid` (`linkid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_usergoodcate` */

/*Table structure for table `1106111321_usergoodlist` */

DROP TABLE IF EXISTS `1106111321_usergoodlist`;

CREATE TABLE `1106111321_usergoodlist` (
  `goodlistid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `cateid` int(10) DEFAULT NULL,
  `catename` varchar(20) NOT NULL,
  `goodname` varchar(100) DEFAULT NULL,
  `sortid` int(10) DEFAULT '100',
  `is_wholesale` int(1) DEFAULT '0',
  `price` decimal(18,2) DEFAULT NULL,
  `wholesaleid` int(10) DEFAULT NULL,
  `cost` decimal(18,2) DEFAULT '0.00',
  `is_state` int(1) DEFAULT '1',
  `kucun_status` int(1) DEFAULT '0',
  `kucun_report` int(10) DEFAULT NULL,
  `report_status` int(1) DEFAULT '2',
  `is_coupon` int(1) DEFAULT '0',
  `is_email` int(1) DEFAULT '0',
  `psd_limit` int(1) DEFAULT '0',
  `psd` varchar(20) DEFAULT NULL,
  `contact_limit` int(1) DEFAULT '0',
  `paytheme` varchar(20) DEFAULT NULL,
  `siteurl` varchar(50) DEFAULT NULL,
  `sitename` varchar(20) DEFAULT NULL,
  `qq` char(11) DEFAULT NULL,
  `linkid` char(16) DEFAULT NULL,
  `define` varchar(20) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  `is_trush` int(1) DEFAULT '0',
  `search_tips` int(10) DEFAULT NULL,
  `remark` varchar(200) DEFAULT NULL,
  `instrucation` varchar(200) DEFAULT NULL,
  `prefix_name` varchar(20) DEFAULT NULL,
  `prefix_password` varchar(20) DEFAULT NULL,
  `low_limit` int(10) DEFAULT NULL,
  `max_limit` int(10) DEFAULT NULL,
  `is_sub` int(1) DEFAULT '0',
  `sub_code` varchar(20) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `is_author` int(1) DEFAULT '0',
  `pr_name` varchar(100) DEFAULT NULL,
  `pr_price` decimal(18,2) DEFAULT '0.00',
  PRIMARY KEY (`goodlistid`),
  KEY `linkid` (`linkid`),
  KEY `goodlistid` (`userid`,`goodlistid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_usergoodlist` */

/*Table structure for table `1106111321_userlog` */

DROP TABLE IF EXISTS `1106111321_userlog`;

CREATE TABLE `1106111321_userlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  `logip` char(15) DEFAULT NULL,
  `ipint` int(11) unsigned DEFAULT NULL,
  `logtime` datetime DEFAULT NULL,
  `timeint` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `username` (`username`,`ipint`,`timeint`),
  KEY `ipint` (`ipint`,`timeint`),
  KEY `timeint` (`timeint`),
  KEY `username_2` (`username`,`timeint`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_userlog` */

/*Table structure for table `1106111321_userpay` */

DROP TABLE IF EXISTS `1106111321_userpay`;

CREATE TABLE `1106111321_userpay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `ptype` tinyint(1) DEFAULT '0',
  `realname` varchar(20) DEFAULT NULL,
  `idcard` varchar(18) DEFAULT NULL,
  `bankname` varchar(20) DEFAULT NULL,
  `card` varchar(20) DEFAULT NULL,
  `bankaddr` varchar(50) DEFAULT NULL,
  `alipay` varchar(50) DEFAULT NULL,
  `weixin` varchar(50) DEFAULT NULL,
  `alipayimg` varchar(255) DEFAULT NULL,
  `weixinimg` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `userid_2` (`userid`,`ptype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_userpay` */

/*Table structure for table `1106111321_userpaychannel` */

DROP TABLE IF EXISTS `1106111321_userpaychannel`;

CREATE TABLE `1106111321_userpaychannel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `channelid` int(10) DEFAULT NULL,
  `channelname` varchar(20) DEFAULT NULL,
  `payway` varchar(20) DEFAULT NULL,
  `userrate` decimal(6,4) DEFAULT NULL,
  `is_state` int(1) DEFAULT '1',
  `is_show` int(1) DEFAULT '1',
  `is_use` int(1) DEFAULT '0',
  `is_trush` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`,`is_state`),
  KEY `userid_2` (`userid`,`channelid`,`is_state`),
  KEY `payway` (`payway`,`is_state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_userpaychannel` */

/*Table structure for table `1106111321_userpayment` */

DROP TABLE IF EXISTS `1106111321_userpayment`;

CREATE TABLE `1106111321_userpayment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `payorder` varchar(20) DEFAULT NULL,
  `usermoney` decimal(18,2) DEFAULT NULL,
  `fee` decimal(6,2) DEFAULT '0.00',
  `settlement` decimal(18,2) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `is_user` int(1) DEFAULT '0',
  `is_state` int(1) DEFAULT '0',
  `ptype` int(1) DEFAULT '0',
  `updatetime` datetime DEFAULT NULL,
  `remark` varchar(500) DEFAULT NULL,
  `remark2` varchar(200) DEFAULT NULL,
  `is_sub` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `status` (`is_user`,`is_state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_userpayment` */

/*Table structure for table `1106111321_userproduct` */

DROP TABLE IF EXISTS `1106111321_userproduct`;

CREATE TABLE `1106111321_userproduct` (
  `productid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `goodlistid` int(10) DEFAULT NULL,
  `orderlistid` int(10) DEFAULT NULL,
  `buylistid` int(10) DEFAULT NULL,
  `cardnumber` varchar(200) DEFAULT NULL,
  `cardpassword` varchar(200) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `deletetime` datetime DEFAULT NULL,
  `is_state` int(1) DEFAULT '1',
  `is_trush` int(1) DEFAULT '0',
  PRIMARY KEY (`productid`),
  KEY `get_orderdetail_on_home` (`productid`,`orderlistid`,`goodlistid`,`is_state`,`is_trush`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_userproduct` */

/*Table structure for table `1106111321_userreportresult` */

DROP TABLE IF EXISTS `1106111321_userreportresult`;

CREATE TABLE `1106111321_userreportresult` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report_orderid` char(16) DEFAULT NULL,
  `report_type` varchar(255) DEFAULT NULL,
  `maijia_contact` varchar(255) DEFAULT NULL,
  `maijia_telephone` varchar(20) DEFAULT NULL,
  `maijia_ip` varchar(20) DEFAULT NULL,
  `maijia_pay` decimal(18,2) DEFAULT '0.00',
  `maijia_contact_pay` varchar(20) DEFAULT NULL,
  `maijia_account` varchar(20) DEFAULT NULL,
  `seller_userid` int(10) DEFAULT NULL,
  `seller_username` varchar(255) DEFAULT NULL,
  `report_creattime` datetime DEFAULT NULL,
  `report_finishtime` datetime DEFAULT NULL,
  `report_status` tinyint(1) DEFAULT '0',
  `report_pay_img` varchar(255) DEFAULT NULL,
  `maijia_code` int(6) DEFAULT NULL,
  `maijia_remark` varchar(255) DEFAULT NULL,
  `result_remark` varchar(255) DEFAULT NULL,
  `result_status` tinyint(1) DEFAULT '0',
  `refund_status` tinyint(1) DEFAULT '0',
  `refund_price` decimal(7,2) DEFAULT NULL,
  `refund_account` varchar(32) DEFAULT NULL,
  `refund_remark` varchar(255) DEFAULT NULL,
  `refund_time` datetime DEFAULT NULL,
  `admin_userid` int(10) DEFAULT NULL,
  `admin_username` varchar(32) DEFAULT NULL,
  `admin_contact_qq` varchar(32) DEFAULT NULL,
  `admin_contact_tel` varchar(20) DEFAULT NULL,
  `admin_contact_remark` varchar(255) DEFAULT NULL,
  `admin_finished_time` datetime DEFAULT NULL,
  `admin_is_read` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `report_orderid` (`report_orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_userreportresult` */

/*Table structure for table `1106111321_usersettlement` */

DROP TABLE IF EXISTS `1106111321_usersettlement`;

CREATE TABLE `1106111321_usersettlement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `is_pay` int(1) DEFAULT '0',
  `paymentid` int(10) DEFAULT NULL,
  `payorder` varchar(20) DEFAULT NULL,
  `paymoney` decimal(18,2) DEFAULT '0.00',
  `is_user` int(1) DEFAULT '0',
  `totlepay` decimal(18,2) DEFAULT '0.00',
  `totlefee` decimal(6,2) DEFAULT '0.00',
  `totlesettlement` decimal(18,2) DEFAULT '0.00',
  `addtime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `is_state` int(1) DEFAULT '0',
  `scopefrom` datetime DEFAULT NULL,
  `scopeto` datetime DEFAULT NULL,
  `balance` decimal(18,2) DEFAULT NULL,
  `income` decimal(18,2) DEFAULT NULL,
  `totalincome` decimal(18,2) DEFAULT NULL,
  `selecttime` datetime DEFAULT NULL,
  `frontmoney` decimal(18,2) DEFAULT '0.00',
  `flag` int(1) DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid_3` (`userid`),
  KEY `userid` (`userid`),
  KEY `userid_2` (`userid`,`is_pay`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_usersettlement` */

/*Table structure for table `1106111321_userwholesale` */

DROP TABLE IF EXISTS `1106111321_userwholesale`;

CREATE TABLE `1106111321_userwholesale` (
  `wholesaleid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) DEFAULT NULL,
  `goodlistid` int(10) DEFAULT NULL,
  `define_quantity` int(10) DEFAULT NULL,
  `define_price` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`wholesaleid`),
  KEY `userid` (`userid`,`goodlistid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `1106111321_userwholesale` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
