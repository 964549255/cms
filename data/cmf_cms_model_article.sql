/*
Navicat MySQL Data Transfer

Source Server         : MYSQL57
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : zy

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-02-04 12:52:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cmf_cms_model_article
-- ----------------------------
DROP TABLE IF EXISTS `cmf_cms_model_article`;
CREATE TABLE `cmf_cms_model_article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thumb` text COMMENT '缩略图',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `summary` text COMMENT '摘要',
  `content` text COMMENT '内容',
  `insert_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  `author` varchar(255) DEFAULT NULL COMMENT '作者',
  `sort` int(11) DEFAULT '1000' COMMENT '排序',
  `category_id` int(11) DEFAULT NULL COMMENT '所属栏目',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
