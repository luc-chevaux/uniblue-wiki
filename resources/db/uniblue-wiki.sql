-- phpMyAdmin SQL Dump
-- version 4.0.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: Nov 07, 2013 alle 08:21
-- Versione del server: 5.5.34-0ubuntu0.13.04.1
-- Versione PHP: 5.4.9-4ubuntu2.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `uniblue-wiki`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `wiki_pages`
--

CREATE TABLE IF NOT EXISTS `wiki_pages` (
  `wiki_id` int(11) NOT NULL AUTO_INCREMENT,
  `wiki_title` varchar(255) NOT NULL,
  `wiki_body` text NOT NULL,
  `wiki_persistent_url` varchar(255) NOT NULL,
  `wiki_visits_count` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`wiki_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='this is the wiki table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `wiki_users`
--

CREATE TABLE IF NOT EXISTS `wiki_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='this is the user table' AUTO_INCREMENT=2 ;

--
-- Dump dei dati per la tabella `wiki_users`
--

INSERT INTO `wiki_users` (`user_id`, `user_name`, `user_password`) VALUES
(1, 'uniblue', '7a3bbf84a14b52436f1ed887c510a611');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
