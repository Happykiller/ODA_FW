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