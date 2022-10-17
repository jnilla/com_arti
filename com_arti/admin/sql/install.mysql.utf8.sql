-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 09, 2022 at 12:51 AM
-- Server version: 8.0.29-0ubuntu0.20.04.3
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `jnillacontactform`
--

-- --------------------------------------------------------

--
-- Table structure for table `#__jnillacontactform_form`
--

DROP TABLE IF EXISTS `#__jnillacontactform_form`;
CREATE TABLE `#__jnillacontactform_form` (
  `id` int UNSIGNED NOT NULL,
  `form_id` int NOT NULL,
  `form_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status_details` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `body` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `api_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date` datetime NOT NULL,
  `notes` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `form_url` text COLLATE utf8mb4_general_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `#__jnillacontactform_form`
--
ALTER TABLE `#__jnillacontactform_form`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `#__jnillacontactform_form`
--
ALTER TABLE `#__jnillacontactform_form`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;
