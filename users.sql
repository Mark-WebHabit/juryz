-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2024 at 10:20 AM
-- Server version: 8.0.34
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `juryz_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `fname` varchar(20) NOT NULL,
  `lname` varchar(20) NOT NULL,
  `email` varchar(60) NOT NULL,
  `date_joined` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `role` varchar(10) NOT NULL DEFAULT 'client'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `date_joined`, `password`, `contact`, `status`, `role`) VALUES
(9, 'Mark', 'Luiz', 'markbobis173@gmail.com', '2024-02-11 14:46:04', '$2y$10$rjBh/Y9ewfnGT/2Mcw7WTuAeQbRU351I3pCXd9BR9x0B5VNC9ETHm', '09512895391', 0, 'admin'),
(10, 'John', 'Doe', 'john@gmail.com', '2024-02-12 19:42:08', '$2y$10$lxkVVXnvTMFO6oeIBIzIuOZD/iHI9nd1HZ6U1hbHlwUAUwu4fmi/e', '0976384637183', 0, 'driver'),
(11, 'Hello', 'World', 'hello@gmail.com', '2024-02-12 19:42:50', '$2y$10$48FDqwp9uSHiJmmk4.KKmuvQY1uQXR3vZjb78i6HdNTLNnQqu5dwO', '09763745291', 0, 'driver'),
(14, 'Luiz', 'Mark', 'luiz@gmail.com', '2024-02-12 19:43:31', '$2y$10$Sekp8o4HhVyq1z/5RLOtwudXn7KE1IlSi7VJGRrLonnaakQqXJwWO', '09751827364', 0, 'client'),
(15, 'Simp', 'Kas', 'simp@gmail.com', '2024-02-12 19:44:32', '$2y$10$fuY9WtPBXWsG743EcJ1XUesPAoriCSjDzu3166PYFB7xDCArekl96', '0982734658367', 0, 'client');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
