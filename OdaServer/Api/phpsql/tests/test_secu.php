<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/test_secu";
$params->modePublic = false;
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/tests/test_secu.php?milis=123450&ctrl=ok&keyAuthODA=42c643cc44c593c5c2b4c5f6d40489dd

//--------------------------------------------------------------------------
//Pour test on récupère les paramètres de l'appli
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "SELECT * 
    FROM `api_tab_parametres` a
    WHERE 1=1
    AND a.`param_name` = :param_name
;";
$params->bindsValue = ["param_name" => "maintenance"];
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);