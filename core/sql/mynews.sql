-- $Id: mynews.sql 477 2005-08-17 20:51:49Z mmcmurr $

-- MySQL dump 9.11
--
-- Host: localhost    Database: mynews
-- ------------------------------------------------------
-- Server version	4.0.20-max-log

--
-- Table structure for table `authors`
--

CREATE TABLE authors (
  artnr int(10) unsigned NOT NULL auto_increment,
  name varchar(70) NOT NULL default '',
  bio text NOT NULL,
  status varchar(50) NOT NULL default '',
  email varchar(50) NOT NULL default '',
  url varchar(50) NOT NULL default '',
  date varchar(50) NOT NULL default '',
  active varchar(50) NOT NULL default '',
  user varchar(16) NOT NULL default '',
  password varchar(32) default NULL,
  PRIMARY KEY  (artnr),
  UNIQUE KEY user (user)
) TYPE=MyISAM;

--
-- Dumping data for table `authors`
--

INSERT INTO authors VALUES (1,'Admin','','Admin','alien@alienated.org','http://www.alienated.org/','2001-08-21 12:12:37','Yes','admin','e99a18c428cb38d5f260853678922e03');

--
-- Table structure for table `calendar`
--

CREATE TABLE calendar (
  eid int(11) NOT NULL auto_increment,
  month int(2) NOT NULL default '0',
  day int(2) NOT NULL default '0',
  year int(4) NOT NULL default '0',
  type varchar(12) NOT NULL default '',
  title varchar(20) NOT NULL default '',
  descrip text NOT NULL,
  userid varchar(16) NOT NULL default '0',
  recurring tinytext,
  active int(1) NOT NULL default '0',
  PRIMARY KEY  (eid)
) TYPE=MyISAM;

--
-- Dumping data for table `calendar`
--


--
-- Table structure for table `comments`
--

CREATE TABLE comments (
  cmtnr int(10) unsigned NOT NULL auto_increment,
  pid int(10) NOT NULL default '0',
  cmtitle varchar(70) NOT NULL default '',
  commenttext text NOT NULL,
  cmauthor varchar(50) NOT NULL default '',
  cmemail varchar(50) NOT NULL default '',
  cmdate datetime NOT NULL default '0000-00-00 00:00:00',
  cmip varchar(50) NOT NULL default '',
  artnr int(10) unsigned NOT NULL default '0',
  type varchar(50) NOT NULL default '',
  PRIMARY KEY  (cmtnr),
  KEY artnr (artnr)
) TYPE=MyISAM;

--
-- Dumping data for table `comments`
--

INSERT INTO comments VALUES (3,0,'Welcome to MyNews','Congratulations!  I knew you could do it!','alien','alien@alienated.org','2002-02-25 22:31:35','12.238.234.106',1,'news');

--
-- Table structure for table `news`
--

CREATE TABLE news (
  artnr int(10) unsigned NOT NULL auto_increment,
  title varchar(70) NOT NULL default '',
  previewtext text NOT NULL,
  ednote text NOT NULL,
  author varchar(50) NOT NULL default '',
  email varchar(50) NOT NULL default '',
  date datetime NOT NULL default '0000-00-00 00:00:00',
  section varchar(50) NOT NULL default '',
  active tinyint(1) unsigned NOT NULL default '1',
  viewcount int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (artnr),
  FULLTEXT KEY author (author,previewtext)
) TYPE=MyISAM;

--
-- Dumping data for table `news`
--

INSERT INTO news VALUES (1,'Welcome to MyNews','Congratulations.  You\'re either up and running or almost there.  Be sure to read the README file and create a new user under the Admin->Authors section.\r\n\r\nI would suggest setting up an Author account (see the README section on Permissions) to begin.\r\n\r\nAgain, congratulations and to see a working example check out http://www.alienated.org or if you have any questions, email me at alien@alienated.org\r\n\r\n\r\nThanks,\r\nMike\r\n\r\n[pagebreak]\r\nLorem ipsum dolor sit amet, consectetaur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.Lorem ipsum dolor sit amet, consectetaur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo. http://www.alienated.org/\r\n\r\n[caption align=\"left\"]\r\n<center>\r\n<img src=\"http://www.alienated.org/uploads/ar3s/album/thumbnails/touristt.jpg\">\r\n</center>\r\n<hr>\r\n<small>This is an example of a caption with the alignment set to left.</small>\r\n[/caption]\r\n\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.Lorem ipsum dolor sit amet, consectetaur adipisicing elit, sed do eiusmod <a href=\"http://alienated.org\">tempor incididunt ut labore et dolore magna aliqua.</a> Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\n\r\n[caption]\r\n<center><img src=\"http://www.alienated.org/uploads/ar3s/album/thumbnails/touristt.jpg\">\r\n<hr />\r\n</center>\r\n<small>This is an example of a caption with default alignment.</small>\r\n[/caption]\r\n\r\ncillum dolore eu fugiat nulla pariatur.Lorem ipsum dolor sit amet, consectetaur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.\r\n\r\n[pagebreak]\r\nLorem ipsum dolor sit amet, consectetaur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.Lorem ipsum dolor sit amet, consectetaur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo.\r\n\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.Lorem ipsum dolor sit amet, consectetaur adipisicing elit, sed do eiusmod <a href=\"http://alienated.org\">tempor incididunt ut labore et dolore magna aliqua.</a> Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\n\r\ncillum dolore eu fugiat nulla pariatur.Lorem ipsum dolor sit amet, consectetaur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.','<b>* Note:</b> Editors and Admins can create \"Editors Notes\" on any story in the system.\r\n\r\n<b>* Note:</b> This story has examples of how to create pagebreaks and captions.','admin','alien@alienated.org','2002-02-21 15:22:22','News',1,19);

--
-- Table structure for table `sections`
--

CREATE TABLE sections (
  secid int(10) unsigned NOT NULL auto_increment,
  section varchar(20) NOT NULL default '',
  front int(1) NOT NULL default '0',
  PRIMARY KEY  (secid),
  UNIQUE KEY section (section)
) TYPE=MyISAM;

--
-- Dumping data for table `sections`
--

INSERT INTO sections VALUES (2,'News',1);
INSERT INTO sections VALUES (4,'Contributed',0);
INSERT INTO sections VALUES (1,'Features',0);
INSERT INTO sections VALUES (3,'Reviews',0);

--
-- Table structure for table `submissions`
--

CREATE TABLE submissions (
  artnr int(10) unsigned NOT NULL auto_increment,
  title varchar(70) NOT NULL default '',
  previewtext text NOT NULL,
  htmltext text NOT NULL,
  author varchar(50) NOT NULL default '',
  email varchar(50) NOT NULL default '',
  date varchar(50) NOT NULL default '',
  section varchar(50) NOT NULL default '',
  PRIMARY KEY  (artnr)
) TYPE=MyISAM;

--
-- Dumping data for table `submissions`
--

INSERT INTO submissions VALUES (1,'This is a test submission','Lorem ipsum dolor sit amet, consectetur adipscing elit, sed diam nonnumy eiusmod tempor incidunt ut labore et dolore magna aliquam erat volupat.\r\n\r\nEt harumd dereud facilis est er expedit distinct. Nam liber a tempor cum soluta nobis eligend optio comque nihil quod a impedit anim id quod maxim placeat facer possim omnis es voluptas assumenda est, omnis dolor repellend.  Temporem autem quinsud et aur office debit aut tum rerum necesit atib saepe eveniet ut er repudiand sint et molestia non este recusand.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipscing elit, sed diam nonnumy eiusmod tempor incidunt ut labore et dolore magna aliquam erat volupat.\r\n\r\nEt harumd dereud facilis est er expedit distinct. Nam liber a tempor cum soluta nobis eligend optio comque nihil quod a impedit anim id quod maxim placeat facer possim omnis es voluptas assumenda est, omnis dolor repellend.  Temporem autem quinsud et aur office debit aut tum rerum necesit atib saepe eveniet ut er repudiand sint et molestia non este recusand.','','alien','alien@alienated.org','2004-07-29 12:48:36','Contributed');

