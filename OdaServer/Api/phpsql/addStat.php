<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/addStat";
$params->arrayInput = array("user","page","action");
$params->modeDebug = false;
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// addStat.php?milis=123450&ctrl=ok&user=ADMI&page=page_home.html&action=checkAuth%20:%20ok

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "INSERT INTO `api_tab_statistiques_site`
    (`date`, `code_user`, `page`, `action`) 
    VALUES 
    (NOW(), :user, :page, :nature)
;";
$params->bindsValue = [
    "user" => $ODA_INTERFACE->inputs["user"],
    "page" => $ODA_INTERFACE->inputs["page"],
    "nature" => $ODA_INTERFACE->inputs["action"]
];
$params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);   

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultatInsert";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);

//---------------------------------------------------------------------------