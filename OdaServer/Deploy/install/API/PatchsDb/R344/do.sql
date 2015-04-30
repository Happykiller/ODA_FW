RENAME TABLE `tab_menu` TO `api_tab_menu`;
RENAME TABLE `tab_menu_categorie` TO `api_tab_menu_categorie`;
RENAME TABLE `tab_menu_rangs_droit` TO `api_tab_menu_rangs_droit`;
RENAME TABLE `tab_rangs` TO `api_tab_rangs`;
RENAME TABLE `tab_service_mail` TO `api_tab_service_mail`;
RENAME TABLE `tab_service_mail_dest` TO `api_tab_service_mail_dest`;
RENAME TABLE `tab_statistiques_site` TO `api_tab_statistiques_site`;
RENAME TABLE `tab_utilisateurs` TO `api_tab_utilisateurs`;
RENAME TABLE `tab_parametres` TO `api_tab_parametres`;

INSERT INTO `api_tab_service_mail` (`id`, `libelle`) VALUES
(1, 'SYSTEM_USER_ACTION');

INSERT INTO `api_tab_service_mail_dest` (`id`, `id_type_mail`, `code_user`, `nivo`) VALUES
(1, 1, 'ADMI', '1');