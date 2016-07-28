-- MySQL dump 10.9
--
-- Host: localhost    Database: mynews_test
-- ------------------------------------------------------
-- Server version	4.1.14-max

--
-- Table structure for table `mnfgal_images`
--

DROP TABLE IF EXISTS `mnfgal_images`;
CREATE TABLE `mnfgal_images` (
  `pid` int(10) unsigned NOT NULL auto_increment,
  `filename` varchar(60) NOT NULL default '',
  `author` varchar(50) NOT NULL default '',
  `album` varchar(60) default NULL,
  `title` varchar(40) NOT NULL default '',
  `descr` text NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`pid`),
  UNIQUE KEY `filename` (`filename`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
