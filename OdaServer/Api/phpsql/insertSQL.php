<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/insertSQL";
$params->modePublic = false;
$params->arrayInput = array("sql");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/insertSQL.php?milis=123456789&sql=INSERT INTO `api_tab_log` (`id` ,`dateTime` ,`type` ,`commentaires`)VALUES (NULL ,  NOW(),  '1',  'essai');

//--------------------------------------------------------------------------
if (preg_match("/\bDELETE\b/i", $ODA_INTERFACE->inputs["sql"])) {
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
$params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultatInsert";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);
