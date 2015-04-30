<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/deleteSQL";
$params->modePublic = false;
$params->arrayInput = array("sql");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/deleteSQL.php?milis=123456789&sql=DELETE FROM `api_tab_log` WHERE 1=0;

//--------------------------------------------------------------------------
if (preg_match("/\INSERT\b/i", $ODA_INTERFACE->inputs["sql"])) {
    die('Non autorisé');
}

if (preg_match("/\bDROP\b/i", $ODA_INTERFACE->inputs["sql"])) {
    die('Non autorisé');
}

if (preg_match("/\bUPDATE\b/i", $ODA_INTERFACE->inputs["sql"])) {
    die('Non autorisé');
}

if (preg_match("/\bSELECT\b/i", $ODA_INTERFACE->inputs["sql"])) {
    die('Non autorisé');
}

$strSql = stripslashes($ODA_INTERFACE->inputs["sql"]);

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = $strSql;
$params->typeSQL = OdaLibBd::SQL_SCRIPT;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);