<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/setChangeProfile";
$params->arrayInput = array("code_user","mdp","champs","value");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/setChangeProfile.php?milis=123450&code_user=VIS&mdp=VIS&champs=prenom&value=kikoo
    
//--------------------------------------------------------------------------
$params = new \stdClass();
$params->nameObj = "api_tab_utilisateurs";
$params->keyObj = ["code_user" => $ODA_INTERFACE->inputs["code_user"]];
$retour = $ODA_INTERFACE->BD_ENGINE->getSingleObject($params);

if($retour->password != $ODA_INTERFACE->inputs["mdp"]){
    $ODA_INTERFACE->dieInError('Mot de passe éronné.');
}else{
    $params = new \stdClass();
    $params->nameObj = "api_tab_utilisateurs";
    $params->keyObj = ["code_user" => $ODA_INTERFACE->inputs["code_user"]];
    $params->setObj = [$ODA_INTERFACE->inputs["champs"] => $ODA_INTERFACE->inputs["value"]];
    $retour = $ODA_INTERFACE->BD_ENGINE->setSingleObj($params);
    
    $params = new \stdClass();
    $params->label = "resultat";
    $params->value = $retour;
    $ODA_INTERFACE->addDataStr($params);
}