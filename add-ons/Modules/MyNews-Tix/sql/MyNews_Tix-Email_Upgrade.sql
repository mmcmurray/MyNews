ALTER TABLE `tickets` ADD `is_email` TINYINT( 1 ) UNSIGNED DEFAULT '0' NOT NULL ;
ALTER TABLE `modifications` ADD `is_email` TINYINT( 1 ) UNSIGNED DEFAULT '0' NOT NULL ;

--
-- Table structure for table `email_users`
--

DROP TABLE IF EXISTS `email_users`;
CREATE TABLE `email_users` (
  `id` tinyint(5) NOT NULL auto_increment,
  `addr` varchar(64) NOT NULL default '',
  `active` binary(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `addr` (`addr`)
) ENGINE=MyISAM;
