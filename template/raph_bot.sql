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
-- Base de données : `raph_bot`
--

-- --------------------------------------------------------

--
-- Structure de la table `alias_commands`
--

CREATE TABLE `alias_commands` (
  `#` int(11) NOT NULL,
  `UUID` varchar(36) NOT NULL,
  `alias` varchar(30) NOT NULL,
  `command` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `commands`
--

CREATE TABLE `commands` (
  `#` int(11) NOT NULL,
  `UUID` varchar(36) NOT NULL,
  `command` varchar(30) NOT NULL,
  `text` text NOT NULL,
  `auto` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `config`
--

CREATE TABLE `config` (
  `#` int(11) NOT NULL,
  `UUID` varchar(36) NOT NULL,
  `id` varchar(30) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `config`
--

INSERT INTO `config` (`#`, `UUID`, `id`, `value`) VALUES
(1, 'b139f5fc-720d-4e39-b425-b5137057f2f6', 'twitch_channel', 'Your twitch channel'),
(2, 'b139f5fc-720d-4e39-b425-b5137057f2f6', 'cmd_prefix', '!'),
(3, 'b139f5fc-720d-4e39-b425-b5137057f2f6', 'twitch_connection_message', 'Your welcome message'),
(4, 'b139f5fc-720d-4e39-b425-b5137057f2f6', 'cmd_time_interval', '0'),
(5, 'b139f5fc-720d-4e39-b425-b5137057f2f6', 'cmd_msg_interval', '0');

-- --------------------------------------------------------

--
-- Structure de la table `ports`
--

CREATE TABLE `ports` (
  `UUID` varchar(36) NOT NULL,
  `port` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `ports`
--

INSERT INTO `ports` (`UUID`, `port`) VALUES
('b139f5fc-720d-4e39-b425-b5137057f2f6', 3000);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `UUID` varchar(36) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`UUID`, `username`, `password`) VALUES
('b139f5fc-720d-4e39-b425-b5137057f2f6', 'admin', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `alias_commands`
--
ALTER TABLE `alias_commands`
  ADD PRIMARY KEY (`#`);

--
-- Index pour la table `commands`
--
ALTER TABLE `commands`
  ADD PRIMARY KEY (`#`);

--
-- Index pour la table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`#`);

--
-- Index pour la table `ports`
--
ALTER TABLE `ports`
  ADD PRIMARY KEY (`UUID`),
  ADD UNIQUE KEY `port` (`port`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UUID`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `alias_commands`
--
ALTER TABLE `alias_commands`
  MODIFY `#` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `commands`
--
ALTER TABLE `commands`
  MODIFY `#` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `config`
--
ALTER TABLE `config`
  MODIFY `#` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `ports`
--
ALTER TABLE `ports`
  MODIFY `port` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3001;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
