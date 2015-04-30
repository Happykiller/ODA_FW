SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données: `@dbLog@`
--

DELIMITER ;

-- --------------------------------------------------------
--
-- Création de la database
--
create database `@dbLog@` CHARACTER SET utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------
--
-- Création de l'utilisateur
--
grant all privileges on `@dbLog@`.* to "@dbLog@"@"@dbDomaine@" identified by "@dbPass@";

-- --------------------------------------------------------
--
-- Interclassement
--
ALTER DATABASE `@dbLog@`
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_menu_categorie`
-- Réservé 1-9 API
-- Réservé 10-19 API_RH
-- Réservé 70-79 Projet
-- Réservé 98 Caché
-- Réservé 99 Liens externes
--
CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_menu_categorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Description` varchar(50) NOT NULL,
  `ouvert` int(2) NOT NULL,
  PRIMARY KEY (`id`)
);

--
-- Réserve un plage de tag pour le système
--
ALTER TABLE `@dbLog@`.`@prefixeDb@api_tab_menu_categorie` AUTO_INCREMENT = 50;

-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_menu`
--
CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Description` varchar(250) NOT NULL,
  `Description_courte` varchar(50) NOT NULL,
  `id_categorie` int(5) NOT NULL,
  `Lien` text NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `index_id_categorie` (  `id_categorie` )
);

--
-- Réserve un plage de tag pour le système
-- Réservé 1-19 API
-- Réservé 20-29 API_RH
-- Réservé : 70-89 Projet
-- Réservé : 100+ pour le projet
--
ALTER TABLE `@dbLog@`.`@prefixeDb@api_tab_menu` AUTO_INCREMENT = 100;

-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_rangs`
--
CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_rangs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `labelle` varchar(250) NOT NULL,
  `indice` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE  `unique_indice` (  `indice` )
);

--
-- Réserve un plage de tag pour le système
--
ALTER TABLE `@dbLog@`.`@prefixeDb@api_tab_rangs` AUTO_INCREMENT = 20;

-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_menu_rangs_droit`
--
CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_menu_rangs_droit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_rang` int(5) NOT NULL,
  `id_menu` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `index_id_categorie` (  `id_rang` )
);

-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_parametres`
--
CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_parametres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `param_name` varchar(50) NOT NULL,
  `param_type` varchar(100) NOT NULL,
  `param_value` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
);

--
-- Réserve un plage pour le système
--
ALTER TABLE `@dbLog@`.`@prefixeDb@api_tab_parametres` AUTO_INCREMENT = 100;

-- -------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_session`
--
CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `datas` text NOT NULL,
  `dateCreation` datetime NOT NULL,
  `periodeValideMinute` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_utilisateurs`
--
CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `code_user` varchar(10) NOT NULL,
  `nom` varchar(20) NOT NULL,
  `prenom` varchar(20) NOT NULL,
  `profile` int(4) NOT NULL,
  `description` varchar(250) NOT NULL,
  `montrer_aide_ihm` int(2) NOT NULL DEFAULT '1',
  `mail` varchar(100) NOT NULL,
  `actif` int(2) NOT NULL,
  `date_creation` datetime NOT NULL,
  `date_modif` datetime NOT NULL,
  `theme` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `index_profile` (  `profile` ),
  UNIQUE `unique_code_user` (  `code_user` )
);

-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_log`
--
CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dateTime` datetime NOT NULL,
  `type` int(4) NOT NULL,
  `commentaires` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
);

-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_log_type`
--
CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_log_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_contact`
--
CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_enreg` datetime NOT NULL,
  `reponse` varchar(250) NOT NULL,
  `message` text NOT NULL,
  `code_user` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
);

-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_statistiques_site`
--
CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_statistiques_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `code_user` varchar(50) NOT NULL,
  `page` varchar(250) NOT NULL,
  `action` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `index_code_user` (  `code_user` )
);

-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_api_tab_service_mail_dest`
--
CREATE TABLE `@dbLog@`.`@prefixeDb@api_tab_service_mail` (
  id int(11) NOT NULL AUTO_INCREMENT,
  libelle varchar(100) NOT NULL,
  PRIMARY KEY (id)
);

--
-- Réserve un plage pour le système
--
ALTER TABLE `@dbLog@`.`@prefixeDb@api_tab_service_mail` AUTO_INCREMENT = 100;

-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_api_tab_service_mail_dest`
--
CREATE TABLE `@dbLog@`.`@prefixeDb@api_api_tab_service_mail_dest` (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_type_mail int(11) NOT NULL,
  code_user varchar(50) NOT NULL,
  nivo varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `index_id_categorie` (  `id_type_mail` ),
  INDEX `index_code_user` (  `code_user` )
);

-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_transaction`
--
CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `statut` varchar(255) NOT NULL,
  `input` text NOT NULL,
  `output` MEDIUMTEXT NOT NULL,
  `debut` datetime NOT NULL,
  `fin` datetime NOT NULL,
  PRIMARY KEY (`id`)
);

-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_messages`
--

CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actif` tinyint(2) NOT NULL,
  `message` varchar(500) NOT NULL,
  `profile` tinyint(2) NOT NULL,
  `niveau` varchar(100) NOT NULL,
  `date_expiration` date NOT NULL,
  `code_user_creation` varchar(100) NOT NULL,
  `date_creation` datetime NOT NULL,
  PRIMARY KEY (`id`)
);

--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_messages_lus`
--

CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_messages_lus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_user` varchar(10) NOT NULL,
  `id_message` int(11) NOT NULL,
  `datelu` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
);

-- --------------------------------------------------------
--
-- Contraites
--
ALTER TABLE  `@dbLog@`.`@prefixeDb@api_tab_menu` ADD FOREIGN KEY (  `id_categorie` ) REFERENCES  `@dbLog@`.`@prefixeDb@api_tab_menu_categorie` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION ;

ALTER TABLE  `@dbLog@`.`@prefixeDb@api_tab_menu_rangs_droit` ADD FOREIGN KEY (  `id_rang` ) REFERENCES  `@dbLog@`.`@prefixeDb@api_tab_rangs` (`indice`) ON DELETE NO ACTION ON UPDATE NO ACTION ;

ALTER TABLE  `@dbLog@`.`@prefixeDb@api_api_tab_service_mail_dest` ADD FOREIGN KEY (  `id_type_mail` ) REFERENCES  `@dbLog@`.`@prefixeDb@api_tab_service_mail` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION ;

ALTER TABLE  `@dbLog@`.`@prefixeDb@api_api_tab_service_mail_dest` ADD FOREIGN KEY (  `code_user` ) REFERENCES  `@dbLog@`.`@prefixeDb@api_tab_utilisateurs` (`code_user`) ON DELETE NO ACTION ON UPDATE NO ACTION ;

ALTER TABLE  `@dbLog@`.`@prefixeDb@api_tab_statistiques_site` ADD FOREIGN KEY (  `code_user` ) REFERENCES  `@dbLog@`.`@prefixeDb@api_tab_utilisateurs` (`code_user`) ON DELETE NO ACTION ON UPDATE NO ACTION ;

ALTER TABLE  `@dbLog@`.`@prefixeDb@api_tab_utilisateurs` ADD FOREIGN KEY (  `profile` ) REFERENCES  `@dbLog@`.`@prefixeDb@api_tab_rangs` (`indice`) ON DELETE NO ACTION ON UPDATE NO ACTION ;