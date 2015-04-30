<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/insertContact";
$params->arrayInput = array("reponse","message","code_user");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/insertContact.php?milis=123456789&reponse=moi@gma.com&message=Ecrit moi !&code_user=FRO

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "INSERT INTO `api_tab_contact`
    (`date_enreg`,`reponse`,`message`,`code_user`) 
    VALUES  
    (NOW(), :reponse, :message, :code_user)
;";
$params->bindsValue = [
    "reponse" => $ODA_INTERFACE->inputs["reponse"],
    "message" => $ODA_INTERFACE->inputs["message"],
    "code_user" => $ODA_INTERFACE->inputs["code_user"],
];
$params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultat";
$params->value = $retour->data;
$ODA_INTERFACE->addDataStr($params);