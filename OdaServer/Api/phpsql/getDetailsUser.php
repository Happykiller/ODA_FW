<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "phpsql/getAuthInfo";
$params->arrayInput = array("code_user", "profile");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/getDetailsUser.php?milis=123450&profile=1&code_user=VIS

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "Select *
    FROM `api_tab_utilisateurs` a
    WHERE 1=1
    AND a.`code_user` = :code_user
;";
$params->bindsValue = ["code_user" => $ODA_INTERFACE->inputs["code_user"]];
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "detailsUser";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "Select *
    FROM `api_tab_rangs` a
    WHERE 1=1
    AND a.`indice` >= :profile
    ORDER BY a.`indice` desc
;";
$params->bindsValue = ["profile" => $ODA_INTERFACE->inputs['profile']];
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "lesRangs";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);