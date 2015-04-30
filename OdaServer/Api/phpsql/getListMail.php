<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "phpsql/getListMail";
$params->arrayInput = array();
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/getListMail.php?milis=123450

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "SELECT a.`code_user`, a.`mail`
    FROM `api_tab_utilisateurs` a
    WHERE 1=1
    AND a.`actif` = 1
    ORDER BY a.`code_user`
;";
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);