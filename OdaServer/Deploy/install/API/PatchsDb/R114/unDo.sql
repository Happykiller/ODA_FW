-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@tab_parametres`
-- Réservé 1-20 API
--
DELETE FROM `@dbLog@`.`@prefixeDb@tab_parametres`
WHERE 1=1
AND `param_name` in ('API_REV_INIT','API_REV');