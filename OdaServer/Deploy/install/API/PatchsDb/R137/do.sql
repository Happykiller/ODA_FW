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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;