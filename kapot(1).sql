-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 06, 2023 at 11:09 AM
-- Server version: 5.7.31
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kapot`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `userid` varchar(500) NOT NULL,
  `password` varchar(500) NOT NULL,
  `status` varchar(200) NOT NULL COMMENT 'enable/disable',
  `action` varchar(100) NOT NULL DEFAULT 'added' COMMENT 'added/updated',
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `userid`, `password`, `status`, `action`, `dt`) VALUES
(1, 'Rachna', 'pragmatestmail@gmail.com', 'admin', 'admin@12345', 'enable', 'added', '2023-01-06 09:06:42');

-- --------------------------------------------------------

--
-- Table structure for table `area`
--

DROP TABLE IF EXISTS `area`;
CREATE TABLE IF NOT EXISTS `area` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `area_name` varchar(1000) NOT NULL,
  `pincode` varchar(500) NOT NULL,
  `city` int(11) NOT NULL,
  `action` varchar(200) NOT NULL DEFAULT 'added' COMMENT 'added/updated',
  `dt` timestamp NOT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
CREATE TABLE IF NOT EXISTS `city` (
  `city_id` int(11) NOT NULL AUTO_INCREMENT,
  `city_name` varchar(500) NOT NULL,
  `status` varchar(200) NOT NULL COMMENT 'enable/disable',
  `action` varchar(200) NOT NULL DEFAULT 'added',
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`city_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`city_id`, `city_name`, `status`, `action`, `dt`) VALUES
(2, 'mumbai', 'enable', 'added', '2023-01-06 10:23:07');

-- --------------------------------------------------------

--
-- Table structure for table `customer_address`
--

DROP TABLE IF EXISTS `customer_address`;
CREATE TABLE IF NOT EXISTS `customer_address` (
  `ca_id` int(11) NOT NULL AUTO_INCREMENT,
  `cust_id` int(11) NOT NULL,
  `address_label` varchar(200) NOT NULL,
  `house_no` varchar(1000) NOT NULL,
  `street` varchar(1000) NOT NULL,
  `area_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `pincode` varchar(500) NOT NULL,
  `action` varchar(200) NOT NULL DEFAULT 'added',
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ca_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_reg`
--

DROP TABLE IF EXISTS `customer_reg`;
CREATE TABLE IF NOT EXISTS `customer_reg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `password` varchar(500) NOT NULL,
  `contact` varchar(200) NOT NULL,
  `status` varchar(200) NOT NULL COMMENT 'enable/disable',
  `action` varchar(200) NOT NULL DEFAULT 'added' COMMENT 'added/updated',
  `dt` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_boy`
--

DROP TABLE IF EXISTS `delivery_boy`;
CREATE TABLE IF NOT EXISTS `delivery_boy` (
  `db_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `password` varchar(500) NOT NULL,
  `contact` varchar(500) NOT NULL,
  `addess` varchar(1000) NOT NULL,
  `city` int(11) NOT NULL,
  `pincode` varchar(200) NOT NULL,
  `id_proof_type` varchar(500) NOT NULL,
  `id_proof` varchar(1000) NOT NULL,
  `status` varchar(200) NOT NULL COMMENT 'enable/disable',
  `action` varchar(200) NOT NULL DEFAULT 'added',
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`db_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
