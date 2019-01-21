CREATE DATABASE IF NOT EXISTS `test`;

USE `test`;

DROP TABLE IF EXISTS `name`;

CREATE TABLE `name` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

INSERT INTO `name` VALUES (7,127,"singi",14);


