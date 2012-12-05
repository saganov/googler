-- MySQL dump x.xx

--
-- Host: localhost    Database: googler
-- -------------------------------------------------------
-- Server version       x.xx.xx

--
-- Table structure for table `search_item`
--
DROP TABLE IF EXISTS `search_item`;
CREATE TABLE `search_item` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `query_phrase` INTEGER NOT NULL DEFAULT 0,
  `source_domain` INTEGER NOT NULL DEFAULT 0,
  `url` VARCHAR(100) NOT NULL DEFAULT '',
  `title` VARCHAR(100) NOT NULL DEFAULT '',
  `description` TEXT,
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `show` INTEGER DEFAULT 0,
  `click` INTEGER DEFAULT 0,
  KEY QUERY_INDEX (`query_phrase`),
  KEY DOMAIN_INDEX (`source_domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `search_item`
--
INSERT INTO `search_item` VALUES
(NULL,1,1,'http://super.domain.com/test','Test on super.domain.com','Description of the Test on super.domain.com',NULL,1,1),
(NULL,1,1,'http://super.domain.com/test2','Test Second on super.domain.com','Description of the Test 2 on super.domain.com',NULL,2,1),
(NULL,1,1,'http://super.domain.com/test3','Test Third on super.domain.com','Description of the Test 3 on super.domain.com',NULL,3,1);

--
-- Table structure for table `news_item`
--
DROP TABLE IF EXISTS `news_item`;
CREATE TABLE `news_item` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `query_phrase` INTEGER NOT NULL DEFAULT 0,
  `url` VARCHAR(100) NOT NULL DEFAULT '',
  `title` VARCHAR(100) NOT NULL DEFAULT '',
  `description` TEXT,
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `show` INTEGER DEFAULT 0,
  `click` INTEGER DEFAULT 0,
  KEY QUERY_INDEX (`query_phrase`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `query_phrase`
--
DROP TABLE IF EXISTS `query_phrase`;
CREATE TABLE `query_phrase` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `text` VARCHAR(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `query_phrase`
--
INSERT INTO `query_phrase` VALUES (NULL, 'test');

--
-- Table structure for table `source_domain`
--
DROP TABLE IF EXISTS `source_domain`;
CREATE TABLE `source_domain` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `domain` VARCHAR(40) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `source_domain`
--
INSERT INTO `source_domain` VALUES
(NULL, 'en.wikipedia.org'),
(NULL, 'ru.wikipedia.org'),
(NULL, 'lurkmore.to');

DROP TABLE IF EXISTS `statistic`;
CREATE TABLE `statistic` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `client` VARCHAR(40) NOT NULL,
  `table` VARCHAR(40) NOT NULL,
  `item_id` INTEGER NOT NULL,
  `shown` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `clicked` TIMESTAMP DEFAULT 0,
  KEY CLIENT_INDEX (`client`),
  KEY SEARCH_INDEX (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

