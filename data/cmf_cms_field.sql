/*
Navicat MySQL Data Transfer

Source Server         : MYSQL57
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : zy

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-02-04 12:50:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cmf_cms_field
-- ----------------------------
DROP TABLE IF EXISTS `cmf_cms_field`;
CREATE TABLE `cmf_cms_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '字段编号',
  `name` varchar(255) DEFAULT NULL COMMENT '字段名称',
  `field` varchar(255) DEFAULT NULL COMMENT '字段键名',
  `type` int(11) DEFAULT NULL COMMENT '字段类型（1-数值型 2-短文本 3-长文本 4-富文本 5-图片）',
  `default` varchar(255) DEFAULT NULL COMMENT '字段默认',
  `length` int(11) DEFAULT '0' COMMENT '字段长度',
  `vital` int(11) DEFAULT '-1' COMMENT '关键字段（1-是 -1-否）',
  `status` int(11) DEFAULT '1' COMMENT '字段状态（1-启用 -1-禁用）',
  `sort` int(11) DEFAULT '1000' COMMENT '字段排序',
  `model_id` int(11) DEFAULT NULL COMMENT '所属模型',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmf_cms_field
-- ----------------------------
INSERT INTO `cmf_cms_field` VALUES ('10', '所属栏目', 'category_id', '1', '', '0', '1', '1', '1000', '1');
INSERT INTO `cmf_cms_field` VALUES ('9', '排序', 'sort', '1', '1000', '0', '1', '1', '1000', '1');
INSERT INTO `cmf_cms_field` VALUES ('8', '作者', 'author', '2', '', '255', '1', '1', '1000', '1');
INSERT INTO `cmf_cms_field` VALUES ('7', '修改时间', 'update_time', '1', '', '0', '1', '1', '1000', '1');
INSERT INTO `cmf_cms_field` VALUES ('6', '添加时间', 'insert_time', '1', '', '0', '1', '1', '1000', '1');
INSERT INTO `cmf_cms_field` VALUES ('5', '内容', 'content', '4', '', '0', '1', '1', '1000', '1');
INSERT INTO `cmf_cms_field` VALUES ('4', '摘要', 'summary', '3', '', '0', '1', '1', '1000', '1');
INSERT INTO `cmf_cms_field` VALUES ('3', '关键词', 'keyword', '2', '', '255', '1', '1', '1000', '1');
INSERT INTO `cmf_cms_field` VALUES ('2', '标题', 'title', '2', '', '255', '1', '1', '1000', '1');
INSERT INTO `cmf_cms_field` VALUES ('1', '缩略图', 'thumb', '5', '', '0', '1', '1', '1000', '1');
INSERT INTO `cmf_cms_field` VALUES ('11', '缩略图', 'thumb', '5', '', '0', '1', '1', '1000', '2');
INSERT INTO `cmf_cms_field` VALUES ('12', '标题', 'title', '2', '', '255', '1', '1', '1000', '2');
INSERT INTO `cmf_cms_field` VALUES ('13', '关键词', 'keyword', '2', '', '255', '1', '1', '1000', '2');
INSERT INTO `cmf_cms_field` VALUES ('14', '摘要', 'summary', '3', '', '0', '1', '1', '1000', '2');
INSERT INTO `cmf_cms_field` VALUES ('15', '内容', 'content', '4', '', '0', '1', '1', '1000', '2');
INSERT INTO `cmf_cms_field` VALUES ('16', '添加时间', 'insert_time', '1', '', '0', '1', '1', '1000', '2');
INSERT INTO `cmf_cms_field` VALUES ('17', '修改时间', 'update_time', '1', '', '0', '1', '1', '1000', '2');
INSERT INTO `cmf_cms_field` VALUES ('18', '作者', 'author', '2', '', '255', '1', '1', '1000', '2');
INSERT INTO `cmf_cms_field` VALUES ('19', '排序', 'sort', '1', '1000', '0', '1', '1', '1000', '2');
INSERT INTO `cmf_cms_field` VALUES ('20', '所属栏目', 'category_id', '1', '', '0', '1', '1', '1000', '2');
INSERT INTO `cmf_cms_field` VALUES ('21', '图片组', 'images', '6', '', '0', '1', '1', '1000', '2');
