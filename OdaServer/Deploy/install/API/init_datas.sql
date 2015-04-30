SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données: `@dbLog@`
--

-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@api_tab_menu_categorie`
-- Réservé 1-9 API
-- Réservé 10-19 API_RH
-- Réservé 70-98 Projet
-- Réservé 98 Liens cachés
-- Réservé 99 Liens externes
--
INSERT INTO `@dbLog@`.`@prefixeDb@api_tab_menu_categorie` (`id`, `Description`, `ouvert`) VALUES
(1, 'L''accueil', 0),
(2, 'Administration', 0),
(3, 'Gestion', 0),
(4, 'Rapports', 0),
(98, 'Cachés', 0),
(99, 'Liens externes', 0);

-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@api_tab_menu`
-- Réservé 1-19 API
-- Réservé 20-29 API_RH
-- Réservé : 70+ Projet
--
INSERT INTO `@dbLog@`.`@prefixeDb@api_tab_menu` (`id`, `Description`, `Description_courte`, `id_categorie`, `Lien`) VALUES
(0, 'Exemple', 'Exemple', 98, 'page_exemple.html'),
(1, 'L''accueil', 'L''accueil', 1, 'page_home.html'),
(2, 'Contact', 'Contact', 1, 'api_page_contact.html'),
(3, 'FAQ', 'FAQ', 1, 'api_page_faq.html'),
(4, 'Statistiques', 'Statistiques', 2, 'api_stats.html'),
(5, 'Administration', 'Administration', 2, 'api_admin.html'),
(6, 'Supervision', 'Supervision', 2, 'api_supervision.html'),
(7, 'Mon profil', 'Mon profil', 3, 'api_profile.html');

-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@api_tab_rangs`
--
INSERT INTO `@dbLog@`.`@prefixeDb@api_tab_rangs` (`id`, `labelle`, `indice`) VALUES
(1, 'Visiteur', 99),
(2, 'Administrateur', 1),
(3, 'Superviseur', 10),
(4, 'Responsable', 20),
(5, 'Utilisateur', 30);

-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@api_tab_menu_rangs_droit`
--
INSERT INTO `@dbLog@`.`@prefixeDb@api_tab_menu_rangs_droit` (`id`, `id_rang`, `id_menu`) VALUES
(1, 1, ';0;1;2;3;4;5;6;7;'),
(2, 10, ';1;2;3;7;'),
(3, 20, ';1;2;3;7;'),
(4, 30, ';1;2;3;7;'),
(5, 99, ';1;2;3;');

-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@api_tab_parametres`
--
INSERT INTO `@dbLog@`.`@prefixeDb@api_tab_parametres` (`id`, `param_name`, `param_type`, `param_value`) VALUES
(1, 'nom_site', 'varchar', '@labelSite@'),
(2, 'maintenance', 'int', '0'),
(3, 'API_REV_INIT', 'int', '@apiRevInit@'),
(4, 'API_REV', 'int', '@apiRevInit@'),
(5, 'contact_mail_administrateur', 'varchar', '@mail_admin@'),
(6, 'theme_defaut', 'varchar', '');

-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@api_tab_utilisateurs`
--
INSERT INTO `@dbLog@`.`@prefixeDb@api_tab_utilisateurs` (`id`, `login`, `password`, `code_user`, `nom`, `prenom`, `profile`, `montrer_aide_ihm`, `mail`, `actif`) VALUES
(null, 'ADMI', 'pass', 'ADMI', 'Administrateur', '', 1, 0, '@mail_admin@', 1),
(null, 'SUPE', 'pass', 'SUPE', 'Superviseur', 'Superviseur', 10, 1, '@mail_admin@', 1),
(null, 'RESP', 'pass', 'RESP', 'Responsable', 'Responsable', 20, 1, '@mail_admin@', 1),
(null, 'UTIL', 'pass', 'UTIL', 'Utilisateur', 'Utilisateur', 30, 1, '@mail_admin@', 1),
(null, 'VIS', 'VIS', 'VIS', 'Visiteur', 'Visiteur', 99, 1, '@mail_admin@', 1);

-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@api_tab_session`
--

INSERT INTO `@dbLog@`.`@prefixeDb@api_tab_session` (`id`, `key`, `datas`, `dateCreation`, `periodeValideMinute`) VALUES
(1, '42c643cc44c593c5c2b4c5f6d40489dd', '{"code_user" : "passepartout"}', '2013-01-01 00:00:01', 0);


-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@api_tab_statistiques_site`
--
INSERT INTO `@dbLog@`.`@prefixeDb@api_tab_statistiques_site` (`id`, `date`, `code_user`, `page`, `action`) VALUES
(null, '2013-05-29 10:30:35', 'ADMI', 'index.html', 'letMeIn : ok');

-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@api_tab_service_mail`
--
INSERT INTO `@dbLog@`.`@prefixeDb@api_tab_service_mail` (`id`, `libelle`) VALUES (NULL, 'SYSTEM_USER_ACTION');

-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@api_tab_service_mail_dest`
--
INSERT INTO `@dbLog@`.`@prefixeDb@api_tab_service_mail_dest` (`id`, `id_type_mail`, `code_user`, `nivo`) VALUES (NULL, '1', 'ADMI', '1');