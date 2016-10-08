-- MySQL dump 10.10
--
-- Host: localhost    Database: net_spider
-- ------------------------------------------------------
-- Server version	5.0.27-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `grab_history`
--

DROP TABLE IF EXISTS `grab_history`;
CREATE TABLE `grab_history` (
  `id` bigint(20) NOT NULL auto_increment,
  `file_url` varchar(1024) default NULL,
  `url_md5` varchar(40) default NULL,
  PRIMARY KEY  (`id`),
  KEY `file_url_md5` (`url_md5`)
) ENGINE=MyISAM AUTO_INCREMENT=105227 DEFAULT CHARSET=utf8 COMMENT='grab history';

--
-- Table structure for table `pic_gallery`
--

DROP TABLE IF EXISTS `pic_gallery`;
CREATE TABLE `pic_gallery` (
  `id` int(11) NOT NULL auto_increment,
  `file_url_md5` varchar(100) NOT NULL,
  `file_url` varchar(1024) default NULL,
  `file_data` longblob,
  `file_type` varchar(100) default NULL,
  `file_name` varchar(255) default NULL,
  `file_size` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `file_url_md5` (`file_url_md5`)
) ENGINE=MyISAM AUTO_INCREMENT=200797 DEFAULT CHARSET=utf8;

--
-- Table structure for table `web_page`
--

DROP TABLE IF EXISTS `web_page`;
CREATE TABLE `web_page` (
  `id` bigint(20) NOT NULL auto_increment,
  `page_url_md5` varchar(100) NOT NULL,
  `page_url` varchar(1024) default NULL,
  `page_content` longblob,
  `page_pic` varchar(1024) default NULL,
  `page_length` int(11) default NULL,
  `grab_time` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `page_url_md5` (`page_url_md5`)
) ENGINE=MyISAM AUTO_INCREMENT=45394 DEFAULT CHARSET=utf8 COMMENT='网络爬虫数据存储表';
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2008-05-09  5:43:01
