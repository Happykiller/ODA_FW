-- --------------------------------------------------------
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Contenu de la table `@dbLog@`.`@prefixeDb@api_tab_session`
--
INSERT INTO `@dbLog@`.`@prefixeDb@api_tab_session` (`id`, `key`, `datas`, `dateCreation`, `periodeValideMinute`) VALUES
(1, '42c643cc44c593c5c2b4c5f6d40489dd', '{"code_user" : "passepartout"}', '2013-01-01 00:00:01', 0);