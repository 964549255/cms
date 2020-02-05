/*
Navicat MySQL Data Transfer

Source Server         : MYSQL57
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : zy

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-01-18 14:23:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cmf_cms_link
-- ----------------------------
DROP TABLE IF EXISTS `cmf_cms_link`;
CREATE TABLE `cmf_cms_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '链接编号',
  `url` text COMMENT '链接地址',
  `category_id` int(11) DEFAULT NULL COMMENT '所属栏目',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
