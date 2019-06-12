-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  mer. 12 juin 2019 à 17:39
-- Version du serveur :  5.7.19
-- Version de PHP :  7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `leblog`
--

-- --------------------------------------------------------

--
-- Structure de la table `b_article`
--

DROP TABLE IF EXISTS `b_article`;
CREATE TABLE IF NOT EXISTS `b_article` (
  `a_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `a_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `a_date_published` datetime DEFAULT NULL,
  `a_date_created` datetime DEFAULT NULL,
  `a_content` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `a_picture` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `a_categorie` int(10) UNSIGNED NOT NULL,
  `a_author` int(10) UNSIGNED NOT NULL,
  `a_valide` tinyint(1) NOT NULL,
  PRIMARY KEY (`a_id`),
  KEY `fk_b_article_b_categorie_idx` (`a_categorie`),
  KEY `fk_b_article_b_user1_idx` (`a_author`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;



--
-- Structure de la table `b_categorie`
--

DROP TABLE IF EXISTS `b_categorie`;
CREATE TABLE IF NOT EXISTS `b_categorie` (
  `c_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `c_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `c_parent` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`c_id`),
  KEY `fk_b_categorie_b_categorie1_idx` (`c_parent`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;


--
-- Structure de la table `b_comment`
--

DROP TABLE IF EXISTS `b_comment`;
CREATE TABLE IF NOT EXISTS `b_comment` (
  `c_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `c_content` text NOT NULL,
  `c_pseudo` varchar(45) NOT NULL,
  `c_email` varchar(150) NOT NULL,
  `c_date_posted` datetime NOT NULL,
  `c_valide` tinyint(3) UNSIGNED DEFAULT '1',
  `c_article` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`c_id`),
  KEY `fk_b_comment_b_article1_idx` (`c_article`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;


--
-- Structure de la table `b_user`
--

DROP TABLE IF EXISTS `b_user`;
CREATE TABLE IF NOT EXISTS `b_user` (
  `u_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `u_firstname` varchar(150) DEFAULT NULL,
  `u_lastname` varchar(150) DEFAULT NULL,
  `u_email` varchar(150) NOT NULL,
  `u_password` varchar(255) NOT NULL,
  `u_valide` tinyint(4) DEFAULT '1',
  `u_role` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`u_id`),
  UNIQUE KEY `u_email_UNIQUE` (`u_email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `b_user`
--

INSERT INTO `b_user` (`u_id`, `u_firstname`, `u_lastname`, `u_email`, `u_password`, `u_valide`, `u_role`) VALUES
(6, 'Jean', 'Halu', 'login@user.fr', '$2y$10$3g/kTi6vhooxQIC6GLPvFeWoZRAn4pt57MsVawQWsJ.lVmL/ZNvNy', 1, 'ROLE_ADMIN');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
