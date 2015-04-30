INSERT INTO  `@dbLog@`.`@prefixeDb@tab_menu` (
`id` ,
`Description` ,
`Description_courte` ,
`id_categorie` ,
`Lien`
)
VALUES (
'7',  'Mon profil',  'Mon profil',  '3',  'api_profile.html'
);

-- --------------------------------------------------------
--
-- Contenu de la table `@dbLog@`.`@prefixeDb@tab_menu_rangs_droit`
-- Réservé 1-20 API
--
UPDATE `@dbLog@`.`@prefixeDb@tab_menu_rangs_droit`
SET `id_menu` = CONCAT(`id_menu`,"7;")
WHERE `id_rang` in (30,20,10,1)
; 

ALTER TABLE  `@dbLog@`.`@prefixeDb@tab_utilisateurs` ADD  `theme` VARCHAR( 50 ) NOT NULL;