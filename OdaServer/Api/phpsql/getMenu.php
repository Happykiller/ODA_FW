<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/getMenu";
$params->arrayInput = array("rang");
$params->arrayInputOpt = array("id_page" => 0);
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/getMenu.php?milis=123450&rang=30&id_page=1

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "CREATE TEMPORARY TABLE `tmp_tab_menu_categorie`
    SELECT c.`id`, c.`Description`, MAX(c.`ouvert`) as 'ouvert'
     FROM (
            SELECT b.`id`, b.`Description`, IF(a.`id` = :id_page, 1, b.`ouvert`) as 'ouvert'
            FROM  `api_tab_menu` a, `api_tab_menu_categorie` b
            WHERE 1=1
            AND a.`id_categorie` = b.id
    ) c
    GROUP BY c.`id`, c.`Description`
;";
$params->bindsValue = ["id_page" => $ODA_INTERFACE->inputs["id_page"]];
$params->typeSQL = OdaLibBd::SQL_SCRIPT;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "SELECT a.`id`, a.`Description` as 'Description_menu', a.`Description_courte`, a.`Lien`, a.`id_categorie`, b.`Description` as 'Description_cate', b.`ouvert`, IF(a.`id` = :id_page, 1, 0) as 'selected'
    FROM  `api_tab_menu` a, `tmp_tab_menu_categorie` b
    WHERE 1=1
    AND a.`id_categorie` = b.id
    AND EXISTS (SELECT 1 FROM `api_tab_menu_rangs_droit` b WHERE b.`id_menu` like CONCAT('%;',a.`id`,';%') and b.`id_rang` = :rang)
    ORDER BY a.`id_categorie` asc, a.`Description_courte` asc
;";
$params->bindsValue = [
    "rang" => $ODA_INTERFACE->inputs["rang"]
    ,"id_page" => $ODA_INTERFACE->inputs["id_page"]
];
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);