-- --------------------------------------------------------
--
-- Structure de la table `@dbLog@`.`@prefixeDb@api_tab_log_type`
--

DROP TABLE IF NOT EXISTS `@dbLog@`.`@prefixeDb@api_tab_log_type`;

-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@tab_menu`
-- Réservé 1-20 API
--
DELETE FROM `@dbLog@`.`@prefixeDb@tab_menu`
WHERE 1=1
AND `Description` in ('Administration','Supervision');


-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@tab_menu_rangs_droit`
-- Réservé 1-20 API
--
-- MANUELLEMENT : Retirer les id des menus Admin et Supervision du rang 1