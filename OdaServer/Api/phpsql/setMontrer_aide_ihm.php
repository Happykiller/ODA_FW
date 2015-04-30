<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/setMontrer_aide_ihm";
$params->arrayInput = array("code_user","valeur");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/setMontrer_aide_ihm.php?milis=123456789&code_user=VIS&valeur=1

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "UPDATE `api_tab_utilisateurs` 
    SET `montrer_aide_ihm` = :valeur 
    WHERE 1=1
    AND `code_user` = :code_user
;";
$params->typeSQL = OdaLibBd::SQL_SCRIPT;
$params->bindsValue = [
    "code_user" => $ODA_INTERFACE->inputs["code_user"],
    "valeur" => $ODA_INTERFACE->inputs["valeur"]
];
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//---------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultat";
$params->value = $retour->nombre;
$ODA_INTERFACE->addDataStr($params);