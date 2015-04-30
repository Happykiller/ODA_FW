-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_log_type`
--

CREATE TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_log_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Contenu de la table `@dbLog@`.`@prefixeDb@tab_menu`
-- Réservé 1-20 API
--
INSERT INTO `@dbLog@`.`@prefixeDb@tab_menu` (`id`, `Description`, `Description_courte`, `id_categorie`, `Lien`) VALUES
(5, 'Administration', 'Administration', 2, 'page_admin.html'),
(6, 'Supervision', 'Supervision', 2, 'page_supervision.html');

-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@tab_menu_rangs_droit`
-- Réservé 1-20 API
--
UPDATE `@dbLog@`.`@prefixeDb@tab_menu_rangs_droit`
SET `id_menu` = CONCAT(`id_menu`,"5;6;")
WHERE `id_rang` = 1
; 