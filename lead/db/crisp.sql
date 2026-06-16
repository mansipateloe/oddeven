-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2019 at 11:41 PM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crisp`
--

-- --------------------------------------------------------

--
-- Table structure for table `cp_lead`
--

CREATE TABLE `cp_lead` (
  `lid` int(11) NOT NULL,
  `srno` varchar(100) NOT NULL,
  `empcode` varchar(100) NOT NULL,
  `ldate` date NOT NULL,
  `ltime` timestamp NOT NULL DEFAULT current_timestamp(),
  `exname` varchar(100) NOT NULL,
  `moniby` varchar(100) NOT NULL,
  `company` varchar(100) NOT NULL,
  `cperson` varchar(100) NOT NULL,
  `mobile1` varchar(100) NOT NULL,
  `mobile2` varchar(100) NOT NULL,
  `emailid` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `address` longtext NOT NULL,
  `pname` varchar(100) NOT NULL,
  `dpv` varchar(100) NOT NULL,
  `ltype` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cp_lead`
--

INSERT INTO `cp_lead` (`lid`, `srno`, `empcode`, `ldate`, `ltime`, `exname`, `moniby`, `company`, `cperson`, `mobile1`, `mobile2`, `emailid`, `city`, `address`, `pname`, `dpv`, `ltype`) VALUES
(15, '001', '77777-2019-10', '2019-10-01', '2019-09-30 20:00:12', 'payal@gmail.com', '', 'investa', 'rahil patel', '1212121212', '', '', '', '', '', '', '3');

-- --------------------------------------------------------

--
-- Table structure for table `cp_lead_file`
--

CREATE TABLE `cp_lead_file` (
  `id` int(11) NOT NULL,
  `lid` varchar(100) NOT NULL,
  `lfile` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cp_lead_followup`
--

CREATE TABLE `cp_lead_followup` (
  `fid` int(11) NOT NULL,
  `lid` varchar(100) NOT NULL,
  `exname` varchar(100) NOT NULL,
  `ftype` varchar(100) NOT NULL,
  `fdate` date NOT NULL,
  `ftime` varchar(100) NOT NULL,
  `remarks` longtext NOT NULL,
  `nfdate` date NOT NULL,
  `nftime` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cp_lead_followup`
--

INSERT INTO `cp_lead_followup` (`fid`, `lid`, `exname`, `ftype`, `fdate`, `ftime`, `remarks`, `nfdate`, `nftime`) VALUES
(14, '15', 'lipsacbhut@gmail.com', 'Email', '2019-09-30', '02:44 AM', 'testing', '2019-09-30', '02:44 AM');

-- --------------------------------------------------------

--
-- Table structure for table `cp_log`
--

CREATE TABLE `cp_log` (
  `lid` int(11) NOT NULL,
  `exname` varchar(100) NOT NULL,
  `details` longtext NOT NULL,
  `ldate` date NOT NULL,
  `cip` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cp_log`
--

INSERT INTO `cp_log` (`lid`, `exname`, `details`, `ldate`, `cip`) VALUES
(1, 'info@otpl.net', 'Update the setting details.', '2018-09-20', '192.168.1.123'),
(2, 'info@otpl.net', 'Update the profile details.', '2018-09-20', '192.168.1.123'),
(3, 'info@otpl.net', 'Update the profile password details.', '2018-09-20', '192.168.1.123'),
(4, 'info@otpl.net', 'Update the profile details.', '2018-09-20', '192.168.1.123'),
(5, 'info@otpl.net', 'Update the profile details.', '2018-09-20', '192.168.1.123'),
(6, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-09-20', '192.168.1.123'),
(7, 'info@crispcloudsolutions.com', 'Delete user details.', '2018-09-20', '192.168.1.123'),
(8, 'info@crispcloudsolutions.com', 'Update user details.', '2018-09-20', '192.168.1.123'),
(9, 'demo@gmail.com', 'User Login in the system.', '2018-09-20', '192.168.1.123'),
(10, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-09-20', '192.168.1.123'),
(11, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-09-20', '192.168.1.123'),
(12, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-09-20', '192.168.1.123'),
(13, 'info@crispcloudsolutions.com', 'Update the setting details.', '2018-09-20', '192.168.1.123'),
(14, 'info@crispcloudsolutions.com', 'Update the setting details.', '2018-09-20', '192.168.1.123'),
(15, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-09-21', '192.168.1.123'),
(16, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-09-22', '192.168.1.123'),
(17, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-09-25', '192.168.1.123'),
(18, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-09-27', '192.168.1.123'),
(19, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-05', '192.168.1.127'),
(20, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-05', '192.168.1.127'),
(21, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-15', '192.168.1.127'),
(22, 'info@crispcloudsolutions.com', 'Add new user details.', '2018-10-15', '192.168.1.127'),
(23, 'info@crispcloudsolutions.com', 'Update user details.', '2018-10-15', '192.168.1.127'),
(24, 'info@crispcloudsolutions.com', 'Update user details.', '2018-10-15', '192.168.1.127'),
(25, 'info@crispcloudsolutions.com', 'Delete user details.', '2018-10-15', '192.168.1.127'),
(26, 'info@crispcloudsolutions.com', 'Update the profile details.', '2018-10-15', '192.168.1.127'),
(27, 'info@crispcloudsolutions.com', 'Update the profile password details.', '2018-10-15', '192.168.1.127'),
(28, 'df@gfmail.com', 'User Login in the system.', '2018-10-15', '192.168.1.127'),
(29, 'df@gfmail.com', 'Update the profile details.', '2018-10-15', '192.168.1.127'),
(30, 'df@gfmail.com', 'Update the profile details.', '2018-10-15', '192.168.1.127'),
(31, 'df@gfmail.com', 'Update the profile password details.', '2018-10-15', '192.168.1.127'),
(32, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-15', '192.168.1.127'),
(33, 'info@crispcloudsolutions.com', 'Update the setting details.', '2018-10-15', '192.168.1.127'),
(34, 'info@crispcloudsolutions.com', 'Delete region details.', '2018-10-15', '192.168.1.127'),
(35, 'info@crispcloudsolutions.com', 'Delete region details.', '2018-10-15', '192.168.1.127'),
(36, 'info@crispcloudsolutions.com', 'Update user details.', '2018-10-15', '192.168.1.127'),
(37, 'info@crispcloudsolutions.com', 'Add new material details.', '2018-10-15', '192.168.1.127'),
(38, 'info@crispcloudsolutions.com', 'Add new material details.', '2018-10-15', '192.168.1.127'),
(39, 'info@crispcloudsolutions.com', 'Update material details.', '2018-10-15', '192.168.1.127'),
(40, 'info@crispcloudsolutions.com', 'Delete material details.', '2018-10-15', '192.168.1.127'),
(41, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-17', '192.168.1.127'),
(42, 'df@gfmail.com', 'User Login in the system.', '2018-10-18', '192.168.1.127'),
(43, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-19', '192.168.1.127'),
(44, 'developer@vbizsolutions.biz', 'Update the setting details.', '2018-10-20', '192.168.1.127'),
(45, 'developer@vbizsolutions.biz', 'Add new material details.', '2018-10-20', '192.168.1.127'),
(46, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-22', '192.168.1.127'),
(47, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-22', '192.168.1.127'),
(48, 'info@crispcloudsolutions.com', 'Update user details.', '2018-10-22', '192.168.1.127'),
(49, 'demo@gmail.com', 'User Login in the system.', '2018-10-22', '192.168.1.127'),
(50, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-22', '192.168.1.127'),
(51, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-22', '192.168.1.127'),
(52, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-23', '192.168.1.123'),
(53, 'info@crispcloudsolutions.com', 'Lead details transfer.', '2018-10-23', '192.168.1.123'),
(54, 'info@crispcloudsolutions.com', 'Lead details transfer.', '2018-10-23', '192.168.1.123'),
(55, 'info@crispcloudsolutions.com', 'Lead details transfer.', '2018-10-23', '192.168.1.123'),
(56, 'info@crispcloudsolutions.com', 'Lead details transfer.', '2018-10-23', '192.168.1.123'),
(57, 'info@crispcloudsolutions.com', 'Lead details transfer.', '2018-10-23', '192.168.1.123'),
(58, 'info@crispcloudsolutions.com', 'Lead details transfer.', '2018-10-23', '192.168.1.123'),
(59, 'info@crispcloudsolutions.com', 'Lead details transfer.', '2018-10-23', '192.168.1.123'),
(60, 'info@crispcloudsolutions.com', 'Close the lead details.', '2018-10-23', '192.168.1.123'),
(61, 'info@crispcloudsolutions.com', 'Close the lead details.', '2018-10-23', '192.168.1.123'),
(62, 'info@crispcloudsolutions.com', 'Close the lead details.', '2018-10-23', '192.168.1.123'),
(63, 'info@crispcloudsolutions.com', 'Lead status details change.', '2018-10-23', '192.168.1.123'),
(64, 'info@crispcloudsolutions.com', 'Add lead  details.', '2018-10-23', '192.168.1.123'),
(65, 'info@crispcloudsolutions.com', 'Delete lead details.', '2018-10-23', '192.168.1.123'),
(66, 'info@crispcloudsolutions.com', 'Add lead  details.', '2018-10-23', '192.168.1.123'),
(67, 'info@crispcloudsolutions.com', 'Add lead followup details.', '2018-10-23', '192.168.1.123'),
(68, 'info@crispcloudsolutions.com', 'Update lead followup details.', '2018-10-23', '192.168.1.123'),
(69, 'info@crispcloudsolutions.com', 'Update lead followup details.', '2018-10-23', '192.168.1.123'),
(70, 'info@crispcloudsolutions.com', 'Update lead followup details.', '2018-10-23', '192.168.1.123'),
(71, 'info@crispcloudsolutions.com', 'Update lead followup details.', '2018-10-23', '192.168.1.123'),
(72, 'info@crispcloudsolutions.com', 'Update lead followup details.', '2018-10-23', '192.168.1.123'),
(73, 'info@crispcloudsolutions.com', 'Update lead followup details.', '2018-10-23', '192.168.1.123'),
(74, 'info@crispcloudsolutions.com', 'Add lead followup details.', '2018-10-23', '192.168.1.123'),
(75, 'info@crispcloudsolutions.com', 'Delete lead followup details.', '2018-10-23', '192.168.1.123'),
(76, 'info@crispcloudsolutions.com', 'Delete lead followup details.', '2018-10-23', '192.168.1.123'),
(77, 'info@crispcloudsolutions.com', 'Update lead followup details.', '2018-10-23', '192.168.1.123'),
(78, 'info@crispcloudsolutions.com', 'Update lead followup details.', '2018-10-23', '192.168.1.123'),
(79, 'info@crispcloudsolutions.com', 'Add customer  details.', '2018-10-23', '192.168.1.123'),
(80, 'info@crispcloudsolutions.com', 'Update customer details.', '2018-10-23', '192.168.1.123'),
(81, 'info@crispcloudsolutions.com', 'Add customer  details.', '2018-10-23', '192.168.1.123'),
(82, 'info@crispcloudsolutions.com', 'Delete customer details.', '2018-10-23', '192.168.1.123'),
(83, 'info@crispcloudsolutions.com', 'Add new material details.', '2018-10-23', '192.168.1.123'),
(84, 'info@crispcloudsolutions.com', 'Delete material details.', '2018-10-23', '192.168.1.123'),
(85, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-23', '192.168.1.123'),
(86, 'info@crispcloudsolutions.com', 'Change the order status', '2018-10-23', '192.168.1.123'),
(87, 'info@crispcloudsolutions.com', 'Change the order status', '2018-10-23', '192.168.1.123'),
(88, 'info@crispcloudsolutions.com', 'Change the order status', '2018-10-23', '192.168.1.123'),
(89, 'info@crispcloudsolutions.com', 'Change the order status', '2018-10-23', '192.168.1.123'),
(90, 'info@crispcloudsolutions.com', 'Change the order status', '2018-10-23', '192.168.1.123'),
(91, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-24', '192.168.1.123'),
(92, 'info@crispcloudsolutions.com', 'Delete order details.', '2018-10-24', '192.168.1.123'),
(93, 'info@crispcloudsolutions.com', 'Delete order payment details.', '2018-10-24', '192.168.1.123'),
(94, 'info@crispcloudsolutions.com', 'Add goal  details.', '2018-10-24', '192.168.1.123'),
(95, 'info@crispcloudsolutions.com', 'Add goal  details.', '2018-10-24', '192.168.1.123'),
(96, 'info@crispcloudsolutions.com', 'Delete goal details.', '2018-10-24', '192.168.1.123'),
(97, 'info@crispcloudsolutions.com', 'Add goal  details.', '2018-10-24', '192.168.1.123'),
(98, 'info@crispcloudsolutions.com', 'Add goal  details.', '2018-10-24', '192.168.1.123'),
(99, 'info@crispcloudsolutions.com', 'Update  the goal details.', '2018-10-24', '192.168.1.123'),
(100, 'info@crispcloudsolutions.com', 'Delete goal details.', '2018-10-24', '192.168.1.123'),
(101, 'info@crispcloudsolutions.com', 'Delete goal details.', '2018-10-24', '192.168.1.123'),
(102, 'info@crispcloudsolutions.com', 'Add bank account details.', '2018-10-24', '192.168.1.123'),
(103, 'info@crispcloudsolutions.com', 'Update bank account details.', '2018-10-24', '192.168.1.123'),
(104, 'info@crispcloudsolutions.com', 'Delete bank account details.', '2018-10-24', '192.168.1.123'),
(105, 'info@crispcloudsolutions.com', 'Add bank account details.', '2018-10-24', '192.168.1.123'),
(106, 'info@crispcloudsolutions.com', 'Add deposit details.', '2018-10-24', '192.168.1.123'),
(107, 'info@crispcloudsolutions.com', 'Update the deposit details.', '2018-10-24', '192.168.1.123'),
(108, 'info@crispcloudsolutions.com', 'Delete deposit details.', '2018-10-24', '192.168.1.123'),
(109, 'info@crispcloudsolutions.com', 'Add deposit details.', '2018-10-24', '192.168.1.123'),
(110, 'info@crispcloudsolutions.com', 'Add expense details.', '2018-10-24', '192.168.1.123'),
(111, 'info@crispcloudsolutions.com', 'Update the expense details.', '2018-10-24', '192.168.1.123'),
(112, 'info@crispcloudsolutions.com', 'Delete deposit details.', '2018-10-24', '192.168.1.123'),
(113, 'info@crispcloudsolutions.com', 'Add deposit details.', '2018-10-24', '192.168.1.123'),
(114, 'info@crispcloudsolutions.com', 'Update the expense details.', '2018-10-24', '192.168.1.123'),
(115, 'info@crispcloudsolutions.com', 'Update the expense details.', '2018-10-24', '192.168.1.123'),
(116, 'info@crispcloudsolutions.com', 'Update the expense details.', '2018-10-24', '192.168.1.123'),
(117, 'info@crispcloudsolutions.com', 'Update the expense details.', '2018-10-24', '192.168.1.123'),
(118, 'info@crispcloudsolutions.com', 'Update the expense details.', '2018-10-24', '192.168.1.123'),
(119, 'info@crispcloudsolutions.com', 'Update the expense details.', '2018-10-24', '192.168.1.123'),
(120, 'info@crispcloudsolutions.com', 'Delete expense details.', '2018-10-24', '192.168.1.123'),
(121, 'info@crispcloudsolutions.com', 'Add expense details.', '2018-10-24', '192.168.1.123'),
(122, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-25', '192.168.1.123'),
(123, 'info@crispcloudsolutions.com', 'Add deposit details.', '2018-10-25', '192.168.1.123'),
(124, 'info@crispcloudsolutions.com', 'Add expense details.', '2018-10-25', '192.168.1.123'),
(125, 'info@crispcloudsolutions.com', 'Add notice details.', '2018-10-25', '192.168.1.123'),
(126, 'info@crispcloudsolutions.com', 'Add notice details.', '2018-10-25', '192.168.1.123'),
(127, 'info@crispcloudsolutions.com', 'Add notice details.', '2018-10-25', '192.168.1.123'),
(128, 'info@crispcloudsolutions.com', 'Update the notice details.', '2018-10-25', '192.168.1.123'),
(129, 'info@crispcloudsolutions.com', 'Delete notice details.', '2018-10-25', '192.168.1.123'),
(130, 'info@crispcloudsolutions.com', 'Add new user details.', '2018-10-25', '192.168.1.123'),
(131, 'info@crispcloudsolutions.com', 'Add new user details.', '2018-10-25', '192.168.1.123'),
(132, 'info@crispcloudsolutions.com', 'Add new user details.', '2018-10-25', '192.168.1.123'),
(133, 'info@crispcloudsolutions.com', 'Add new user details.', '2018-10-25', '192.168.1.123'),
(134, 'executive@gmail.com', 'User Login in the system.', '2018-10-25', '192.168.1.123'),
(135, 'executive@gmail.com', 'Add report details.', '2018-10-25', '192.168.1.123'),
(136, 'executive@gmail.com', 'Add new user details.', '2018-10-25', '192.168.1.123'),
(137, 'executive2@gmail.com', 'User Login in the system.', '2018-10-25', '192.168.1.123'),
(138, 'executive2@gmail.com', 'Add report details.', '2018-10-25', '192.168.1.123'),
(139, 'coordinator@gmail.com', 'User Login in the system.', '2018-10-25', '192.168.1.123'),
(140, 'coordinator@gmail.com', 'Add report details.', '2018-10-25', '192.168.1.123'),
(141, 'coordinator@gmail.com', 'Add customer  details.', '2018-10-25', '192.168.1.123'),
(142, 'coordinator@gmail.com', 'Add customer  details.', '2018-10-25', '192.168.1.123'),
(143, 'coordinator@gmail.com', 'Add customer  details.', '2018-10-25', '192.168.1.123'),
(144, 'coordinator@gmail.com', 'Add lead  details.', '2018-10-25', '192.168.1.123'),
(145, 'coordinator@gmail.com', 'Add lead  details.', '2018-10-25', '192.168.1.123'),
(146, 'coordinator@gmail.com', 'Add lead  details.', '2018-10-25', '192.168.1.123'),
(147, 'manager@gmail.com', 'User Login in the system.', '2018-10-25', '192.168.1.123'),
(148, 'manager@gmail.com', 'Add Product details.', '2018-10-25', '192.168.1.123'),
(149, 'manager@gmail.com', 'Add Product details.', '2018-10-25', '192.168.1.123'),
(150, 'manager@gmail.com', 'Add Product details.', '2018-10-25', '192.168.1.123'),
(151, 'manager@gmail.com', 'Add Product details.', '2018-10-25', '192.168.1.123'),
(152, 'coordinator@gmail.com', 'User Login in the system.', '2018-10-25', '192.168.1.123'),
(153, 'admin@gmail.com', 'User Login in the system.', '2018-10-25', '192.168.1.123'),
(154, 'admin@gmail.com', 'Add new material details.', '2018-10-25', '192.168.1.123'),
(155, 'admin@gmail.com', 'Add new material details.', '2018-10-25', '192.168.1.123'),
(156, 'manager@gmail.com', 'User Login in the system.', '2018-10-25', '192.168.1.123'),
(157, 'manager@gmail.com', 'Add goal  details.', '2018-10-25', '192.168.1.123'),
(158, 'manager@gmail.com', 'Update  the goal details.', '2018-10-25', '192.168.1.123'),
(159, 'manager@gmail.com', 'Add goal  details.', '2018-10-25', '192.168.1.123'),
(160, 'manager@gmail.com', 'Add bank account details.', '2018-10-25', '192.168.1.123'),
(161, 'manager@gmail.com', 'Add deposit details.', '2018-10-25', '192.168.1.123'),
(162, 'manager@gmail.com', 'Add deposit details.', '2018-10-25', '192.168.1.123'),
(163, 'manager@gmail.com', 'Add expense details.', '2018-10-25', '192.168.1.123'),
(164, 'coordinator@gmail.com', 'User Login in the system.', '2018-10-25', '192.168.1.123'),
(165, 'manager@gmail.com', 'User Login in the system.', '2018-10-25', '192.168.1.123'),
(166, 'coordinator@gmail.com', 'User Login in the system.', '2018-10-25', '192.168.1.123'),
(167, 'executive@gmail.com', 'User Login in the system.', '2018-10-25', '192.168.1.123'),
(168, 'executive@gmail.com', 'Add notice details.', '2018-10-25', '192.168.1.123'),
(169, 'executive@gmail.com', 'Add notice details.', '2018-10-25', '192.168.1.123'),
(170, 'executive@gmail.com', 'Add notice details.', '2018-10-25', '192.168.1.123'),
(171, 'executive@gmail.com', 'User Login in the system.', '2018-10-25', '192.168.1.123'),
(172, 'coordinator@gmail.com', 'User Login in the system.', '2018-10-25', '192.168.1.123'),
(173, 'executive@gmail.com', 'User Login in the system.', '2018-10-30', '192.168.1.123'),
(174, 'executive@gmail.com', 'Add report details.', '2018-10-30', '192.168.1.123'),
(175, 'executive@gmail.com', 'Add report details.', '2018-10-30', '192.168.1.123'),
(176, 'executive@gmail.com', 'Update report details.', '2018-10-30', '192.168.1.123'),
(177, 'coordinator@gmail.com', 'User Login in the system.', '2018-10-30', '192.168.1.123'),
(178, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-30', '192.168.1.123'),
(179, 'info@crispcloudsolutions.com', 'Update product details.', '2018-10-30', '192.168.1.123'),
(180, 'info@crispcloudsolutions.com', 'Update product details.', '2018-10-30', '192.168.1.123'),
(181, 'info@crispcloudsolutions.com', 'Update product details.', '2018-10-30', '192.168.1.123'),
(182, 'info@crispcloudsolutions.com', 'Update product details.', '2018-10-30', '192.168.1.123'),
(183, 'manager@gmail.com', 'User Login in the system.', '2018-10-30', '192.168.1.123'),
(184, 'manager@gmail.com', 'Update  the goal details.', '2018-10-30', '192.168.1.123'),
(185, 'manager@gmail.com', 'Update  the goal details.', '2018-10-30', '192.168.1.123'),
(186, 'manager@gmail.com', 'Lead status details change.', '2018-10-30', '192.168.1.123'),
(187, 'manager@gmail.com', 'Lead status details change.', '2018-10-30', '192.168.1.123'),
(188, 'manager@gmail.com', 'Update bank account details.', '2018-10-30', '192.168.1.123'),
(189, 'manager@gmail.com', 'Add deposit details.', '2018-10-30', '192.168.1.123'),
(190, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-10-30', '192.168.1.123'),
(191, 'developer@vbizsolutions.biz', 'Update lead followup details.', '2018-10-30', '192.168.1.123'),
(192, 'developer@vbizsolutions.biz', 'Update lead followup details.', '2018-10-30', '192.168.1.123'),
(193, 'coordinator@gmail.com', 'User Login in the system.', '2018-10-30', '192.168.1.123'),
(194, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-11-01', '192.168.1.123'),
(195, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-11-02', '192.168.1.123'),
(196, 'info@crispcloudsolutions.com', 'Update lead details.', '2018-11-02', '192.168.1.123'),
(197, 'info@crispcloudsolutions.com', 'Add lead followup details.', '2018-11-02', '192.168.1.123'),
(198, 'info@crispcloudsolutions.com', 'Add lead followup details.', '2018-11-02', '192.168.1.123'),
(199, 'info@crispcloudsolutions.com', 'Delete lead followup details.', '2018-11-02', '192.168.1.123'),
(200, 'info@crispcloudsolutions.com', 'Delete lead followup details.', '2018-11-02', '192.168.1.123'),
(201, 'info@crispcloudsolutions.com', 'Delete lead followup details.', '2018-11-02', '192.168.1.123'),
(202, 'info@crispcloudsolutions.com', 'Add lead followup details.', '2018-11-02', '192.168.1.123'),
(203, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-11-03', '192.168.1.123'),
(204, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-11-14', '192.168.1.123'),
(205, 'info@crispcloudsolutions.com', 'Add Product details.', '2018-11-14', '192.168.1.123'),
(206, 'info@crispcloudsolutions.com', 'Add Product details.', '2018-11-14', '192.168.1.123'),
(207, 'info@crispcloudsolutions.com', 'Delete product details.', '2018-11-14', '192.168.1.123'),
(208, 'info@crispcloudsolutions.com', 'Delete product details.', '2018-11-14', '192.168.1.123'),
(209, 'info@crispcloudsolutions.com', 'Add Product details.', '2018-11-14', '192.168.1.123'),
(210, 'info@crispcloudsolutions.com', 'Update product details.', '2018-11-14', '192.168.1.123'),
(211, 'info@crispcloudsolutions.com', 'Update product details.', '2018-11-14', '192.168.1.123'),
(212, 'info@crispcloudsolutions.com', 'Update product details.', '2018-11-14', '192.168.1.123'),
(213, 'info@crispcloudsolutions.com', 'Update product details.', '2018-11-14', '192.168.1.123'),
(214, 'info@crispcloudsolutions.com', 'Add lead  details.', '2018-11-14', '192.168.1.123'),
(215, 'info@crispcloudsolutions.com', 'Update lead details.', '2018-11-14', '192.168.1.123'),
(216, 'info@crispcloudsolutions.com', 'Delete customer details.', '2018-11-14', '192.168.1.123'),
(217, 'info@crispcloudsolutions.com', 'Add new user details.', '2018-11-14', '192.168.1.123'),
(218, 'info@crispcloudsolutions.com', 'Delete user details.', '2018-11-14', '192.168.1.123'),
(219, 'info@crispcloudsolutions.com', 'Add new user details.', '2018-11-14', '192.168.1.123'),
(220, 'info@crispcloudsolutions.com', 'Update user details.', '2018-11-14', '192.168.1.123'),
(221, 'info@crispcloudsolutions.com', 'Update user details.', '2018-11-14', '192.168.1.123'),
(222, 'info@crispcloudsolutions.com', 'Update user details.', '2018-11-14', '192.168.1.123'),
(223, 'info@crispcloudsolutions.com', 'Add bank account details.', '2018-11-14', '192.168.1.123'),
(224, 'info@crispcloudsolutions.com', 'Update the setting details.', '2018-11-14', '192.168.1.123'),
(225, 'info@crispcloudsolutions.com', 'Update the setting details.', '2018-11-14', '192.168.1.123'),
(226, 'info@crispcloudsolutions.com', 'Update the setting details.', '2018-11-14', '192.168.1.123'),
(227, 'info@crispcloudsolutions.com', 'Update the setting details.', '2018-11-14', '192.168.1.123'),
(228, 'info@crispcloudsolutions.com', 'Update the setting details.', '2018-11-14', '192.168.1.123'),
(229, 'info@crispcloudsolutions.com', 'Update the setting details.', '2018-11-14', '192.168.1.123'),
(230, 'info@crispcloudsolutions.com', 'Update the setting details.', '2018-11-14', '192.168.1.123'),
(231, 'info@crispcloudsolutions.com', 'Update the setting details.', '2018-11-14', '192.168.1.123'),
(232, 'info@crispcloudsolutions.com', 'Update the setting details.', '2018-11-14', '192.168.1.123'),
(233, 'info@crispcloudsolutions.com', 'Update the profile details.', '2018-11-14', '192.168.1.123'),
(234, 'info@crispcloudsolutions.com', 'Update the profile details.', '2018-11-14', '192.168.1.123'),
(235, 'info@crispcloudsolutions.com', 'Update the profile details.', '2018-11-14', '192.168.1.123'),
(236, 'info@crispcloudsolutions.com', 'Update the profile details.', '2018-11-14', '192.168.1.123'),
(237, 'info@crispcloudsolutions.com', 'Update the profile details.', '2018-11-14', '192.168.1.123'),
(238, 'admin@gmail.com', 'User Login in the system.', '2018-11-14', '192.168.1.123'),
(239, 'admin@gmail.com', 'Update the profile details.', '2018-11-14', '192.168.1.123'),
(240, 'admin@gmail.com', 'Update the profile details.', '2018-11-14', '192.168.1.123'),
(241, 'admin@gmail.com', 'Update the profile details.', '2018-11-14', '192.168.1.123'),
(242, 'admin@gmail.com', 'Update the profile details.', '2018-11-14', '192.168.1.123'),
(243, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-11-14', '192.168.1.123'),
(244, 'info@crispcloudsolutions.com', 'Add notice details.', '2018-11-14', '192.168.1.123'),
(245, 'info@crispcloudsolutions.com', 'Add notice details.', '2018-11-14', '192.168.1.123'),
(246, 'info@crispcloudsolutions.com', 'Add notice details.', '2018-11-14', '192.168.1.123'),
(247, 'info@crispcloudsolutions.com', 'Add notice details.', '2018-11-14', '192.168.1.123'),
(248, 'info@crispcloudsolutions.com', 'Add notice details.', '2018-11-14', '192.168.1.123'),
(249, 'info@crispcloudsolutions.com', 'Update the notice details.', '2018-11-14', '192.168.1.123'),
(250, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-11-15', '192.168.1.123'),
(251, 'info@crispcloudsolutions.com', 'Admin Login in the system.', '2018-11-16', '192.168.1.123'),
(252, 'info@crispcloudsolutions.com', 'Add new user details.', '2018-11-16', '192.168.1.123'),
(253, 'info@crispcloudsolutions.com', 'Update user details.', '2018-11-16', '192.168.1.123'),
(254, 'info@crispcloudsolutions.com', 'Update user details.', '2018-11-16', '192.168.1.123'),
(255, 'info@crispcloudsolutions.com', 'Update user details.', '2018-11-16', '192.168.1.123'),
(256, 'info@crispcloudsolutions.com', 'Update user details.', '2018-11-16', '192.168.1.123'),
(257, 'info@crispcloudsolutions.com', 'Add new user details.', '2018-11-16', '192.168.1.123'),
(258, 'admin@gmail.com', 'User Login in the system.', '2019-09-10', '192.168.0.108'),
(259, 'admin@gmail.com', 'User Login in the system.', '2019-09-25', '192.168.0.109'),
(260, 'admin@gmail.com', 'User Login in the system.', '2019-09-28', '192.168.0.109'),
(261, 'admin@gmail.com', 'User Login in the system.', '2019-09-28', '192.168.0.103'),
(262, 'admin@gmail.com', 'Update the setting details.', '2019-09-28', '192.168.0.103'),
(263, 'admin@gmail.com', 'Update the setting details.', '2019-09-28', '192.168.0.103'),
(264, 'admin@gmail.com', 'User Login in the system.', '2019-09-28', '192.168.0.103'),
(265, 'admin@gmail.com', 'User Login in the system.', '2019-09-30', '192.168.0.103'),
(266, 'admin@gmail.com', 'User Login in the system.', '2019-09-30', '192.168.0.103'),
(267, 'admin@gmail.com', 'User Login in the system.', '2019-10-01', '192.168.0.102'),
(268, 'admin@gmail.com', 'Update the setting details.', '2019-10-01', '192.168.0.102'),
(269, 'admin@gmail.com', 'Update the setting details.', '2019-10-01', '192.168.0.102'),
(270, 'admin@gmail.com', 'User Login in the system.', '2019-10-01', '192.168.0.102'),
(271, 'admin@gmail.com', 'Add new user details.', '2019-10-01', '192.168.0.102'),
(272, 'admin@gmail.com', 'Update user details.', '2019-10-01', '192.168.0.102'),
(273, 'admin@gmail.com', 'Add lead  details.', '2019-10-01', '192.168.0.102'),
(274, 'admin@gmail.com', 'Delete lead details.', '2019-10-01', '192.168.0.102'),
(275, 'admin@gmail.com', 'Add lead  details.', '2019-10-01', '192.168.0.102'),
(276, 'lipsacbhut@gmail.com', 'User Login in the system.', '2019-10-01', '192.168.0.102'),
(277, 'lipsacbhut@gmail.com', 'Add lead  details.', '2019-10-01', '192.168.0.102'),
(278, 'lipsacbhut@gmail.com', 'Lead details transfer.', '2019-10-01', '192.168.0.102'),
(279, 'payal@gmail.com', 'User Login in the system.', '2019-10-01', '192.168.43.176'),
(280, 'lipsacbhut@gmail.com', 'User Login in the system.', '2019-10-01', '192.168.43.176'),
(281, 'lipsacbhut@gmail.com', 'Lead status details change.', '2019-10-01', '192.168.43.176'),
(282, 'lipsacbhut@gmail.com', 'Add lead followup details.', '2019-10-01', '192.168.43.176'),
(283, 'admin@gmail.com', 'User Login in the system.', '2019-10-01', '192.168.43.176');

-- --------------------------------------------------------

--
-- Table structure for table `cp_login`
--

CREATE TABLE `cp_login` (
  `id` int(11) NOT NULL,
  `aname` varchar(100) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `desig` varchar(100) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `photo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cp_login`
--

INSERT INTO `cp_login` (`id`, `aname`, `pass`, `password`, `desig`, `fname`, `lname`, `mobile`, `email`, `status`, `photo`) VALUES
(1, 'JRathodA', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'Administrator', 'Jayesh', 'Rathod', '7878787878', 'hello@oddeveninfotech.com', '0', '_1542190387.png');

-- --------------------------------------------------------

--
-- Table structure for table `cp_setting`
--

CREATE TABLE `cp_setting` (
  `sid` int(11) NOT NULL,
  `sname` varchar(100) NOT NULL,
  `svalue` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cp_setting`
--

INSERT INTO `cp_setting` (`sid`, `sname`, `svalue`) VALUES
(1, 'GST', '5'),
(2, 'ADMIN_FEE', '16'),
(3, 'clogo', 'logo.png'),
(4, 'caddress', '122 Shree Ugati Corporate Park'),
(5, 'cemailid', 'hello@oddeveninfotech.com'),
(6, 'cphone', '281-2479955'),
(7, 'cmobile', '8460090038'),
(8, 'cname', 'Oddeven Infotech'),
(9, 'dob', 'Orchid Tours, Wishing you a Very Happy Birthday. As a special birthday treat, we\'re giving you special offer on your next trip.'),
(10, 'doa', 'Orchid Tours, Wishing you a Very Happy Anniversary. As a special birthday treat, we\'re giving you special offer on your next trip.'),
(11, 'inquiry_sms', 'Thank you for visit Orchid Tours Pvt. Ltd. Pls Note Our Mob Number for Further Query. Call : 9824216217');

-- --------------------------------------------------------

--
-- Table structure for table `cp_users`
--

CREATE TABLE `cp_users` (
  `uid` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `utype` varchar(100) NOT NULL,
  `empcode` varchar(100) NOT NULL,
  `emailid` varchar(100) NOT NULL,
  `phoneno` varchar(100) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `photo` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `doj` date NOT NULL,
  `address` longtext NOT NULL,
  `scoordinator` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cp_users`
--

INSERT INTO `cp_users` (`uid`, `fname`, `lname`, `utype`, `empcode`, `emailid`, `phoneno`, `pass`, `password`, `photo`, `status`, `doj`, `address`, `scoordinator`) VALUES
(6, 'Tejpal', 'Navadiya', '1', 'emp001', 'admin@gmail.com', '7878787878', 'e10adc3949ba59abbe56e057f20f883e', '123', '_1542191315.jpg', '0', '2018-10-01', 'Mumbai', ''),
(13, 'Lipsa', 'patel', '2', 'emp002', 'lipsa@gmail.com', '7878787878', '202cb962ac59075b964b07152d234b70', '123', '', '0', '1970-01-01', 'wer', ''),
(15, 'paypall', 'jhal', '3', 'emp003', 'payal@gmail.com', '7777777777', 'a9eb812238f753132652ae09963a05e9', '1237', '', '0', '2019-09-09', 'dsfsfcdzsvvsfs77777777777', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cp_lead`
--
ALTER TABLE `cp_lead`
  ADD PRIMARY KEY (`lid`);

--
-- Indexes for table `cp_lead_file`
--
ALTER TABLE `cp_lead_file`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cp_lead_followup`
--
ALTER TABLE `cp_lead_followup`
  ADD PRIMARY KEY (`fid`);

--
-- Indexes for table `cp_log`
--
ALTER TABLE `cp_log`
  ADD PRIMARY KEY (`lid`);

--
-- Indexes for table `cp_login`
--
ALTER TABLE `cp_login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cp_setting`
--
ALTER TABLE `cp_setting`
  ADD PRIMARY KEY (`sid`);

--
-- Indexes for table `cp_users`
--
ALTER TABLE `cp_users`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cp_lead`
--
ALTER TABLE `cp_lead`
  MODIFY `lid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `cp_lead_file`
--
ALTER TABLE `cp_lead_file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `cp_lead_followup`
--
ALTER TABLE `cp_lead_followup`
  MODIFY `fid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `cp_log`
--
ALTER TABLE `cp_log`
  MODIFY `lid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=284;

--
-- AUTO_INCREMENT for table `cp_login`
--
ALTER TABLE `cp_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cp_setting`
--
ALTER TABLE `cp_setting`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cp_users`
--
ALTER TABLE `cp_users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
