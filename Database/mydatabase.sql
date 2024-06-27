-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2024 at 10:37 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mydatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `tabloid`
--

CREATE TABLE `tabloid` (
  `id` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `Name`, `Email`, `Password`, `Created_at`) VALUES
(1, 'Onah Anthony', 'onah@gmail.com', '1234abcd', '2024-06-26 09:23:26'),
(2, 'Onah Anthony', 'tony@gmail.com', '1234abcd', '2024-06-26 09:26:13'),
(3, 'Nneka Onyeka', 'nneka@mail.com', '1111aaaa', '2024-06-26 13:40:12'),
(4, 'Maazi', 'Onah@mail.com', '55555ggggg', '2024-06-26 16:00:38'),
(5, 'Bloody Hell', 'Hell@mail.com', '1111dddd', '2024-06-26 16:01:35'),
(6, 'Bloody Hell', 'Hell@mail.com', '1111dddd', '2024-06-26 17:10:20'),
(7, 'Shaun', 'shaun@mail.com', '1234bbbb', '2024-06-26 18:52:47'),
(8, 'Name surname', 'name@mail.com', '$2y$10$wfuVdcHrbRo20mu.aX5k6O62K.dExjxWesOUmptqLhcR.mNVMz5VS', '2024-06-26 19:35:06'),
(9, 'Name surname', 'name@mail.com', '$2y$10$zeLCoGl.TgfTJL6S1Hbco.bBFezizEsuNtD7LIXvFoDFoSDf67FNa', '2024-06-26 19:37:08'),
(10, 'Olise promise', 'prom@mail.com', '$2y$10$Q84xNx7C8Ooq8zJUFinPZ.uPSwaxRB8mQz4Qf02rVkL4e4AiD8Dn2', '2024-06-26 20:19:28'),
(11, 'Ada baanyi', 'ada@mail.com', '$2y$10$tBR8lpLhoKKJGHu.cWMQNOWmrT8dE2AKXEZ0ZOipNBI9y3OjA8aOK', '2024-06-26 22:20:17'),
(12, 'SImon Cooper', 'cooper@mail.com', '$2y$10$hDGeO1PZnl2NxYkQAHcJLOTjW.gTx/42PXu.ZNI.Vq9chQElrbVei', '2024-06-27 20:29:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tabloid`
--
ALTER TABLE `tabloid`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tabloid`
--
ALTER TABLE `tabloid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
