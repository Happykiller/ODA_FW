<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/getListutilisateurs";
$params->arrayInput = array("indice_user");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/getListutilisateurs.php?milis=123456789&ctrl=ok&indice_user=10

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "SELECT a.`code_user`, a.`mail`, a.`nom`, a.`prenom`, b.`labelle`, a.`description`, a.`actif`
    FROM `api_tab_utilisateurs` a, `api_tab_rangs` b
    WHERE 1=1
    AND b.`indice` >= :indice
    AND a.`profile` = b.`indice`
    ORDER BY a.`actif`, a.`code_user`
;";
$params->bindsValue = [
    "indice" => $ODA_INTERFACE->inputs["indice_user"]
];
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultats";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);
