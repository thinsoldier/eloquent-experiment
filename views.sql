-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 28, 2017 at 08:36 AM
-- Server version: 5.6.21
-- PHP Version: 5.5.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `eloquent-experiment`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `districts`
--
CREATE TABLE `districts` (
`id` int(6)
,`parent_id` int(2)
,`section` varchar(32)
,`category` varchar(64)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `islands`
--
CREATE TABLE `islands` (
`id` int(6)
,`parent_id` int(2)
,`section` varchar(32)
,`category` varchar(64)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `locations`
--
CREATE TABLE `locations` (
`id` int(6)
,`parent_id` int(2)
,`section` varchar(32)
,`category` varchar(64)
);
-- --------------------------------------------------------

--
-- Structure for view `districts`
--
DROP TABLE IF EXISTS `districts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `districts` AS select `categories`.`id` AS `id`,`categories`.`parent_id` AS `location_id`,`categories`.`section` AS `section`,`categories`.`category` AS `category` from `categories` where (`categories`.`section` = 'district');

-- --------------------------------------------------------

--
-- Structure for view `islands`
--
DROP TABLE IF EXISTS `islands`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `islands` AS select `categories`.`id` AS `id`,`categories`.`section` AS `section`,`categories`.`category` AS `category` from `categories` where (`categories`.`section` = 'island');

-- --------------------------------------------------------

--
-- Structure for view `locations`
--
DROP TABLE IF EXISTS `locations`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `locations` AS select `categories`.`id` AS `id`,`categories`.`parent_id` AS `island_id`,`categories`.`section` AS `section`,`categories`.`category` AS `category` from `categories` where (`categories`.`section` = 'location');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
