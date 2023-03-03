-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 05, 2019 at 10:27 AM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `query_panel`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_query_action_history`
--

CREATE TABLE `tbl_query_action_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `action_date` varchar(100) NOT NULL,
  `query_id` int(11) NOT NULL,
  `query_assign_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_query_action_history`
--

INSERT INTO `tbl_query_action_history` (`id`, `user_id`, `user_name`, `message`, `action_date`, `query_id`, `query_assign_id`) VALUES
(1, 131, 'Priyanka Dolai', 'Create Query and assign to niranjan', '1570192558', 77849, 84705),
(2, 1, 'Admin', 'Update Query', '1570193046', 77849, 84705),
(3, 131, 'Priyanka Dolai', 'Update Query', '1570193490', 77849, 84705),
(4, 1, 'Admin', 'Update Query', '1570194485', 77849, 84705),
(5, 1, 'Admin', 'Query Replicated to Thejaswini', '1570194901', 77849, 84706),
(6, 1, 'Admin', 'Query Transfer to Niranjan Singh', '1570195023', 77849, 84706),
(7, 1, 'Admin', 'Query Transfer to Niranjan Singh', '1570195081', 77849, 84706),
(8, 1, 'Admin', 'Query Transfer to Niranjan Singh', '1570195132', 77847, 84704);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_query_action_history`
--
ALTER TABLE `tbl_query_action_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_query_action_history`
--
ALTER TABLE `tbl_query_action_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
