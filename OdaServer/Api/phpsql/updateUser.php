<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/updateUser";
$params->arrayInput = array("mail","actif", "rang", "code_user");
$params->arrayInputOpt = array("desc"=>null);
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/updateUser.php?milis=123450&code_user=VIS&mail=vis.vis@gmail.com&actif=1&rang=10

//--------------------------------------------------------------------------
$strDesc = "";
if($ODA_INTERFACE->inputs["desc"] != NULL){
    $strDesc = "`description` = '".$ODA_INTERFACE->inputs["desc"]."', ";
}

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "UPDATE `api_tab_utilisateurs`
    SET `mail` = :mail,
    ".$strDesc."
    `actif` = :actif,
    `profile` = :rang,
    `date_modif` = NOW()
    WHERE 1=1
    AND `code_user` = :code_user
;";
$params->typeSQL = OdaLibBd::SQL_SCRIPT;
$params->bindsValue = [
    "code_user" => $ODA_INTERFACE->inputs["code_user"],
    "mail" => $ODA_INTERFACE->inputs["mail"],
    "actif" => $ODA_INTERFACE->inputs["actif"],
    "rang" => $ODA_INTERFACE->inputs["rang"]
];
$params->typeSQL = OdaLibBd::SQL_SCRIPT;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//---------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultatUpdate";
$params->value = $retour->nombre;
$ODA_INTERFACE->addDataStr($params);