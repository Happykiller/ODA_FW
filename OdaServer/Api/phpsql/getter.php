<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/getter";
$params->arrayInput = array("table","get","filtre");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/getter.php?milis=123450&table=api_tab_utilisateurs&get={"champ":"prenom","type":"PARAM_STR"}&filtre={"champ":"code_user","valeur":"VIS","type":"PARAM_STR"}

//--------------------------------------------------------------------------
$jsonGet = json_decode(stripslashes($ODA_INTERFACE->inputs["get"]),true);
$jsonFiltre = json_decode(stripslashes($ODA_INTERFACE->inputs["filtre"]),true);

$strGet = "";
$champGet = $jsonGet["champ"];
$typeGet = $jsonGet["type"];
$strGet .= " `".$champGet."` ";

$strFiltre = "";
$champ = $jsonFiltre["champ"];
$valeur = $jsonFiltre["valeur"];
$type = $jsonFiltre["type"];
if($type == 'PARAM_STR'){
    $strFiltre .= " AND `".$champ."` = '".$valeur."' ";
}else{
    $strFiltre .= " AND `".$champ."` = ".$valeur." ";
}

$strSql = "SELECT ".$strGet." as 'champ', '".$typeGet."' as 'type'
    FROM `".$ODA_INTERFACE->inputs["table"]."` a
    WHERE 1=1
    ".$strFiltre."
";

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = $strSql;
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//---------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultat";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);