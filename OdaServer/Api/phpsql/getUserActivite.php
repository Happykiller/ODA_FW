<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/getUserActivite";
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/getUserActivite.php?milis=123456789&ctrl=ok

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "SELECT IF(a.`code_user`='','N.A',a.`code_user`) as 'code_user', count(*) 'nombre'
    FROM `api_tab_statistiques_site` a, `api_tab_utilisateurs` b
    WHERE 1=1
    AND a.`code_user` = b.`code_user`
    AND b.`profile` > 1
    GROUP BY a.`code_user`
    ORDER BY `nombre` desc
;";
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);