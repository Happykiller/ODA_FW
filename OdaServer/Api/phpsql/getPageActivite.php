<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/getPageActivite";
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/getPageActivite.php?milis=123456789&ctrl=ok

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "SELECT `page`, count(*) 'nombre'
    FROM `api_tab_statistiques_site` a, `api_tab_utilisateurs` b
    WHERE 1=1
    AND a.`code_user` = b.`code_user`
    AND b.`profile` > 1
    GROUP BY a.`page`
    ORDER BY `nombre` desc
;";
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//---------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);