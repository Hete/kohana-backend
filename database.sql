-- phpMyAdmin SQL Dump
-- version 3.5.4
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Sam 12 Janvier 2013 à 07:25
-- Version du serveur: 5.5.28
-- Version de PHP: 5.4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `saveinteam`
--

-- --------------------------------------------------------

--
-- Structure de la table `acquirements`
--

CREATE TABLE IF NOT EXISTS `acquirements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `semaphore_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `semaphore_id` (`semaphore_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Structure de la table `semaphores`
--

CREATE TABLE IF NOT EXISTS `semaphores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(40) NOT NULL,
  `max_acquire` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Contenu de la table `semaphores`
--

INSERT INTO `semaphores` (`id`, `key`, `max_acquire`) VALUES
(23, '7505d64a54e061b7acd54ccd58b49dc43500b635', 1);

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `acquirements`
--
ALTER TABLE `acquirements`
  ADD CONSTRAINT `acquirements_ibfk_1` FOREIGN KEY (`semaphore_id`) REFERENCES `semaphores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
