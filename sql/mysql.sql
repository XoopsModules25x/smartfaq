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
  `categoryid`  INT(11)      NOT NULL AUTO_INCREMENT,
  `parentid`    INT(11)      NOT NULL DEFAULT '0',
  `name`        VARCHAR(100) NOT NULL DEFAULT '',
  `description` TEXT         NOT NULL,
  `total`       INT(11)      NOT NULL DEFAULT '0',
  `weight`      INT(11)      NOT NULL DEFAULT '1',
  `created`     INT(11)      NOT NULL DEFAULT '1033141070',
  PRIMARY KEY (`categoryID`),
  UNIQUE KEY `categoryID` (`categoryID`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 1;
# --------------------------------------------------------

#
# Table structure for table `smartfaq_faq`
#
# Creation: Mar 16, 2004 at 02:04 PM
# Last update: Mar 16, 2004 at 02:04 PM
#

CREATE TABLE `smartfaq_faq` (
  `faqid`       INT(8)          NOT NULL AUTO_INCREMENT,
  `categoryid`  INT(11)         NOT NULL DEFAULT '0',
  `question`    TEXT            NOT NULL,
  `howdoi`      VARCHAR(255)    NOT NULL DEFAULT '',
  `diduno`      TEXT            NOT NULL,
  `uid`         INT(6)                   DEFAULT '0',
  `datesub`     INT(11)         NOT NULL DEFAULT '0',
  `status`      INT(1)          NOT NULL DEFAULT '-1',
  `counter`     INT(8) UNSIGNED NOT NULL DEFAULT '0',
  `weight`      INT(11)         NOT NULL DEFAULT '1',
  `html`        TINYINT(1)      NOT NULL DEFAULT '1',
  `smiley`      TINYINT(1)      NOT NULL DEFAULT '1',
  `xcodes`      TINYINT(1)      NOT NULL DEFAULT '1',
  `image`       TINYINT(1)      NOT NULL DEFAULT '1',
  `linebreak`   TINYINT(1)      NOT NULL DEFAULT '1',
  `cancomment`  TINYINT(1)      NOT NULL DEFAULT '1',
  `comments`    INT(11)         NOT NULL DEFAULT '0',
  `notifypub`   TINYINT(1)      NOT NULL DEFAULT '0',
  `modulelink`  VARCHAR(50)     NOT NULL DEFAULT 'None',
  `contextpage` VARCHAR(255)    NOT NULL DEFAULT '',
  `exacturl`    TINYINT(1)      NOT NULL DEFAULT '0',
  `partialview` TINYINT(1)      NOT NULL DEFAULT '0',
  PRIMARY KEY (`faqid`),
  UNIQUE KEY `faqid` (`faqid`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 1;

#
# Table structure for table `smartfaq_answers`
#
# Creation: Mar 16, 2004 at 02:04 PM
# Last update: Mar 16, 2004 at 02:04 PM
#

CREATE TABLE `smartfaq_answers` (
  `answerid`   INT(11)    NOT NULL AUTO_INCREMENT,
  `faqid`      INT(11)    NOT NULL DEFAULT '0',
  `answer`     TEXT       NOT NULL,
  `uid`        INT(6)              DEFAULT '0',
  `status`     INT(1)     NOT NULL DEFAULT '-1',
  `datesub`    INT(11)    NOT NULL DEFAULT '0',
  `notifypub`  TINYINT(1) NOT NULL DEFAULT '1',
  `attachment` TEXT,
  PRIMARY KEY (`answerid`),
  UNIQUE KEY `answerid` (`answerid`),
  FULLTEXT KEY `answer` (`answer`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 1;

