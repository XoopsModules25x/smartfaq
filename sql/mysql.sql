# phpMyAdmin MySQL-Dump
# version 2.5.0
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: Mar 16, 2004 at 03:09 PM
# Server version: 4.0.18
# PHP Version: 4.3.4
# --------------------------------------------------------

#
# Table structure for table `smartfaq_categories`
#
# Creation: Mar 16, 2004 at 11:14 AM
# Last update: Mar 16, 2004 at 12:31 PM
#

CREATE TABLE `smartfaq_categories` (
  `categoryid` int(11) NOT NULL auto_increment,
  `parentid` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `total` int(11) NOT NULL default '0',
  `weight` int(11) NOT NULL default '1',
  `created` int(11) NOT NULL default '1033141070',
  PRIMARY KEY  (`categoryID`),
  UNIQUE KEY `categoryID` (`categoryID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;
# --------------------------------------------------------

#
# Table structure for table `smartfaq_faq`
#
# Creation: Mar 16, 2004 at 02:04 PM
# Last update: Mar 16, 2004 at 02:04 PM
#

CREATE TABLE `smartfaq_faq` (
  `faqid` int(8) NOT NULL auto_increment,
  `categoryid` int(11) NOT NULL default '0',
  `question` TEXT NOT NULL,
  `howdoi` varchar(255) NOT NULL default '',
  `diduno` TEXT NOT NULL,
  `uid` int(6) default '0',
  `datesub` int(11) NOT NULL default '0',
  `status` int(1) NOT NULL default '-1',
  `counter` int(8) unsigned NOT NULL default '0',
  `weight` int(11) NOT NULL default '1',
  `html` tinyint(1) NOT NULL default '1',
  `smiley` tinyint(1) NOT NULL default '1',
  `xcodes` tinyint(1) NOT NULL default '1',
  `image` tinyint(1) NOT NULL default '1',
  `linebreak` tinyint(1) NOT NULL default '1',
  `cancomment` tinyint(1) NOT NULL default '1',
  `comments` int(11) NOT NULL default '0',
  `notifypub` tinyint(1) NOT NULL default '0',
  `modulelink` varchar(50) NOT NULL default 'None',
  `contextpage` varchar(255) NOT NULL default '',
  `exacturl` tinyint(1) NOT NULL default '0',
  `partialview` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`faqid`),
  UNIQUE KEY `faqid` (`faqid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;


#
# Table structure for table `smartfaq_answers`
#
# Creation: Mar 16, 2004 at 02:04 PM
# Last update: Mar 16, 2004 at 02:04 PM
#

CREATE TABLE `smartfaq_answers` (
  `answerid` int(11) NOT NULL auto_increment,
  `faqid` int(11) NOT NULL default '0',
  `answer` text NOT NULL,
  `uid` int(6) default '0',
  `status` int(1) NOT NULL default '-1',
  `datesub` int(11) NOT NULL default '0',
  `notifypub` tinyint(1) NOT NULL default '1',
  `attachment` 			text,
  PRIMARY KEY  (`answerid`),
  UNIQUE KEY `answerid` (`answerid`),
  FULLTEXT KEY `answer` (`answer`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;

