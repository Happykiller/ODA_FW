<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "phpsql/getAuth";
$params->arrayInput = array("login", "mdp");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/getAuth.php?milis=123450&login=VIS&mdp=VIS

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "select a.`profile`, a.`code_user`
    from `api_tab_utilisateurs` a
    where 1=1 
    and a.`login` = :login
    and a.`password` = :mdp
;";
$params->bindsValue = [
    "login" => $ODA_INTERFACE->inputs["login"]
    , "mdp" => $ODA_INTERFACE->inputs["mdp"]
];
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
//get key
$key = $ODA_INTERFACE->buildSession(array('code_user' => $retour->data->code_user, 'password' => $ODA_INTERFACE->inputs["mdp"]));

$retour->data->keyAuthODA = $key;

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);
