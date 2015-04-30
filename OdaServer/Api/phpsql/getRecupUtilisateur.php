<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/getRecupUtilisateur";
$params->arrayInput = array("identifiant","email");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/getRecupUtilisateur.php?milis=123456789&email=fabrice.rosito@cgi.com&identifiant=codeUtilis

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "SELECT a.`id`, a.`nom`, a.`prenom`, a.`code_user`, a.`password`, a.`mail`
    FROM `api_tab_utilisateurs` a
    WHERE 1=1
    AND a.`actif` = 1
    AND (
        a.`code_user` = :codeUtilisateur
        OR
        a.`mail` = :email
    )
;";
$params->bindsValue = [
    "codeUtilisateur" => $ODA_INTERFACE->inputs["identifiant"]
    , "email" => $ODA_INTERFACE->inputs["email"]
];
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//---------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);