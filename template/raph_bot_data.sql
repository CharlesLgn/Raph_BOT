-- phpMyAdmin SQL Dump
-- version 5.0.4deb2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : lun. 18 avr. 2022 à 15:53
-- Version du serveur :  10.5.12-MariaDB-0+deb11u1
-- Version de PHP : 7.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Déchargement des données de la table `config`
--

INSERT INTO `config` (`#`, `UUID`, `id`, `value`) VALUES
(1, 'b139f5fc-720d-4e39-b425-b5137057f2f6', 'twitch_channel', 'Your twitch channel'),
(2, 'b139f5fc-720d-4e39-b425-b5137057f2f6', 'cmd_prefix', '!'),
(3, 'b139f5fc-720d-4e39-b425-b5137057f2f6', 'twitch_connection_message', 'Your welcome message'),
(4, 'b139f5fc-720d-4e39-b425-b5137057f2f6', 'cmd_time_interval', '0'),
(5, 'b139f5fc-720d-4e39-b425-b5137057f2f6', 'cmd_msg_interval', '0');

--
-- Déchargement des données de la table `ports`
--

INSERT INTO `ports` (`UUID`, `port`) VALUES
('b139f5fc-720d-4e39-b425-b5137057f2f6', 3000);

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`UUID`, `username`, `password`) VALUES
('b139f5fc-720d-4e39-b425-b5137057f2f6', 'admin', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
