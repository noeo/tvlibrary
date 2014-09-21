-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 21. Sep 2014 um 10:24
-- Server Version: 5.6.19
-- PHP-Version: 5.4.30

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `tvhackday2`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `genres`
--

DROP TABLE IF EXISTS `genres`;
CREATE TABLE IF NOT EXISTS `genres` (
  `genres_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `genre` varchar(100) NOT NULL,
  `gtype` varchar(10) NOT NULL,
  PRIMARY KEY (`genres_id`),
  UNIQUE KEY `genre` (`genre`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2353 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ignorelist`
--

DROP TABLE IF EXISTS `ignorelist`;
CREATE TABLE IF NOT EXISTS `ignorelist` (
  `videos_id` bigint(20) NOT NULL DEFAULT '0',
  `users_id` bigint(20) NOT NULL DEFAULT '0',
  UNIQUE KEY `videos_id` (`videos_id`,`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ignorelist_genres`
--

DROP TABLE IF EXISTS `ignorelist_genres`;
CREATE TABLE IF NOT EXISTS `ignorelist_genres` (
  `users_id` bigint(20) NOT NULL DEFAULT '0',
  `genres_id` bigint(20) NOT NULL DEFAULT '0',
  UNIQUE KEY `users_id` (`users_id`,`genres_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `skiplist`
--

DROP TABLE IF EXISTS `skiplist`;
CREATE TABLE IF NOT EXISTS `skiplist` (
  `videosid` varchar(100) NOT NULL DEFAULT '0',
  `videos_id` bigint(20) NOT NULL DEFAULT '0',
  `users_id` bigint(20) NOT NULL DEFAULT '0',
  `session_id` varchar(255) NOT NULL,
  `datecreated` datetime NOT NULL,
  UNIQUE KEY `tvevent_id` (`videosid`,`users_id`,`session_id`,`videos_id`),
  KEY `datecreated` (`datecreated`),
  KEY `videos_id` (`videos_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sources`
--

DROP TABLE IF EXISTS `sources`;
CREATE TABLE IF NOT EXISTS `sources` (
  `sources_id` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(100) NOT NULL,
  `entityid` varchar(255) NOT NULL,
  `use_yes` tinyint(4) NOT NULL DEFAULT '0',
  `ignore` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sources_id`),
  UNIQUE KEY `source` (`source`),
  KEY `use_yes` (`use_yes`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=480 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tvevent`
--

DROP TABLE IF EXISTS `tvevent`;
CREATE TABLE IF NOT EXISTS `tvevent` (
  `tvevent_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `videotitle` varchar(100) NOT NULL,
  `imageurl` varchar(255) NOT NULL,
  `videosid` varchar(255) NOT NULL,
  `watchurl` varchar(255) NOT NULL,
  `description` text,
  `duration` int(11) NOT NULL DEFAULT '0',
  `showtitle` varchar(255) NOT NULL,
  `episode` int(11) DEFAULT NULL,
  `sources_id` int(11) NOT NULL DEFAULT '0',
  `videos_id` bigint(20) NOT NULL DEFAULT '0',
  `broadcastTime` datetime NOT NULL,
  `epgData` text,
  `epgData_yes` tinyint(4) NOT NULL DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  `magnetId` varchar(255) DEFAULT NULL,
  `publicationTime` varchar(255) DEFAULT NULL,
  `fullVideo` tinyint(4) NOT NULL DEFAULT '0',
  `season` varchar(255) DEFAULT NULL,
  `episodeTitle` varchar(255) DEFAULT NULL,
  `preview` varchar(255) DEFAULT NULL,
  `premium` tinyint(4) NOT NULL DEFAULT '0',
  `price` varchar(255) DEFAULT NULL,
  `fileLocations` varchar(255) DEFAULT NULL,
  `broadcaster` varchar(255) DEFAULT NULL,
  `broadcastId` varchar(255) DEFAULT NULL,
  `mobileLicense` varchar(255) DEFAULT NULL,
  `ageRestriction` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`tvevent_id`),
  UNIQUE KEY `videosid` (`videosid`),
  KEY `sources_id` (`sources_id`),
  KEY `videos_id` (`videos_id`),
  KEY `broadcastTime` (`broadcastTime`),
  KEY `fullVideo` (`fullVideo`),
  KEY `preview` (`preview`),
  KEY `epgData_yes` (`epgData_yes`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29129 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `videos`
--

DROP TABLE IF EXISTS `videos`;
CREATE TABLE IF NOT EXISTS `videos` (
  `videos_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `videotitle` varchar(255) NOT NULL,
  `vtype` varchar(10) NOT NULL,
  PRIMARY KEY (`videos_id`),
  UNIQUE KEY `videotitle` (`videotitle`),
  KEY `vtype` (`vtype`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29131 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `videos_genres`
--

DROP TABLE IF EXISTS `videos_genres`;
CREATE TABLE IF NOT EXISTS `videos_genres` (
  `videos_id` bigint(20) NOT NULL DEFAULT '0',
  `genres_id` bigint(20) NOT NULL DEFAULT '0',
  UNIQUE KEY `videos_id` (`videos_id`,`genres_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
