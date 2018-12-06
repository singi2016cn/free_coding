/*
Navicat MySQL Data Transfer

Source Server         : localhost.laragon
Source Server Version : 50719
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50719
File Encoding         : 65001

Date: 2018-12-06 18:02:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for books
-- ----------------------------
DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `int` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `cover` varchar(1023) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `publishing_company` varchar(255) DEFAULT NULL,
  `isbn` varchar(255) DEFAULT NULL,
  `edition` varchar(255) DEFAULT NULL,
  `commodity_code` varchar(255) DEFAULT NULL,
  `packing` varchar(255) DEFAULT NULL,
  `series_name` varchar(255) DEFAULT NULL,
  `format` varchar(255) DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `form` varchar(255) DEFAULT NULL,
  `page` varchar(255) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `resource_url` varchar(1023) DEFAULT NULL,
  `resource_more_file_url` varchar(1023) DEFAULT NULL,
  `from_platform` varchar(255) DEFAULT NULL,
  `content_profile` text,
  `author_profile` text,
  `table_of_contents` text,
  PRIMARY KEY (`int`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;
