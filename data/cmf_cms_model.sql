/*
Navicat MySQL Data Transfer

Source Server         : MYSQL57
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : zy

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-02-04 12:51:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cmf_cms_model
-- ----------------------------
DROP TABLE IF EXISTS `cmf_cms_model`;
CREATE TABLE `cmf_cms_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '模型编号',
  `name` varchar(255) DEFAULT NULL COMMENT '模型名称',
  `field` varchar(255) DEFAULT NULL COMMENT '模型键名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmf_cms_model
-- ----------------------------
INSERT INTO `cmf_cms_model` VALUES ('1', '文章', 'article');
INSERT INTO `cmf_cms_model` VALUES ('2', '图片', 'picture');
