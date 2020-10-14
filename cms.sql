-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 14, 2020 at 03:01 PM
-- Server version: 5.7.24
-- PHP Version: 7.2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id_article` int(3) NOT NULL,
  `id_compte` int(2) NOT NULL,
  `titre_article` varchar(150) NOT NULL,
  `contenu_article` text NOT NULL,
  `date_article` datetime NOT NULL,
  `fichier_article` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `comptes`
--

CREATE TABLE `comptes` (
  `id_compte` int(3) NOT NULL,
  `nom_compte` varchar(50) NOT NULL,
  `prenom_compte` varchar(50) NOT NULL,
  `login_compte` varchar(50) NOT NULL,
  `pass_compte` blob,
  `statut_compte` varchar(30) NOT NULL,
  `fichier_compte` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comptes`
--

INSERT INTO `comptes` (`id_compte`, `nom_compte`, `prenom_compte`, `login_compte`, `pass_compte`, `statut_compte`, `fichier_compte`) VALUES
(1, 'Gourvès', 'Alain', 'admin', 0x30623963323632356463323165663035663661643464646634376335663230333833376161333263, 'admin', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id_contact` int(5) NOT NULL,
  `nom_contact` varchar(50) NOT NULL,
  `prenom_contact` varchar(30) NOT NULL,
  `mel_contact` varchar(250) NOT NULL,
  `message_contact` text NOT NULL,
  `date_contact` datetime NOT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `droits`
--

CREATE TABLE `droits` (
  `id_droits` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `admin` varchar(3) DEFAULT NULL,
  `user` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id_menu` int(11) NOT NULL,
  `intitule_menu` varchar(50) NOT NULL,
  `dashicon_menu` int(100) NOT NULL,
  `rang_menu` int(2) NOT NULL,
  `lien_menu` varchar(100) DEFAULT NULL,
  `type_menu` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id_article`),
  ADD KEY `titre_article` (`titre_article`),
  ADD KEY `id_compte` (`id_compte`);

--
-- Indexes for table `comptes`
--
ALTER TABLE `comptes`
  ADD PRIMARY KEY (`id_compte`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id_contact`);

--
-- Indexes for table `droits`
--
ALTER TABLE `droits`
  ADD PRIMARY KEY (`id_droits`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id_menu`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id_article` int(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comptes`
--
ALTER TABLE `comptes`
  MODIFY `id_compte` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id_contact` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `droits`
--
ALTER TABLE `droits`
  MODIFY `id_droits` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
