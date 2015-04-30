<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/addUtilisateur";
$params->arrayInput = array("nom","prenom","email","motDePasse","codeUtilisateur");
$ODA_INTERFACE = new OdaLibInterface($params);
//--------------------------------------------------------------------------
// addUtilisateur.php?milis=123450&nom=nom&prenom=prenom&email=email@mail.com&motDePasse=mdp&codeUtilisateur=NOP

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "select count(*) as result
    from `api_tab_utilisateurs`
    where 1=1
    AND code_user like '".$ODA_INTERFACE->inputs["codeUtilisateur"]."%'
;";
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);
$nbSamePseudo = intval($retour->data->result);

//--------------------------------------------------------------------------
if($nbSamePseudo == 0){
    $codeUtilisateur = $ODA_INTERFACE->inputs["codeUtilisateur"];
}else{
    $codeUtilisateur = $ODA_INTERFACE->inputs["codeUtilisateur"].$nbSamePseudo;
}

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "INSERT INTO `api_tab_utilisateurs` 
    (`login`,`password`,`code_user`,`nom`,`prenom`,`profile`,`montrer_aide_ihm`,`mail`,`actif`,`date_creation`,`date_modif`) 
    VALUES  
    ( :code_user, :motDePasse, :code_user, :nom, :prenom, 99, 1, :email, 1, now(), now())
;";
$params->bindsValue = [
    "code_user" => $codeUtilisateur
    , "motDePasse" => $ODA_INTERFACE->inputs["motDePasse"]
    , "nom" => $ODA_INTERFACE->inputs["nom"]
    , "prenom" => $ODA_INTERFACE->inputs["prenom"]
    , "email" => $ODA_INTERFACE->inputs["email"]
];
$params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "select a.`id`,a.`code_user`,a.`mail`
    FROM `api_tab_utilisateurs` a
    WHERE 1=1
    AND a.id = :idUser
;";
$params->typeSQL = OdaLibBd::SQL_GET_ONE;
$params->bindsValue = [
    "idUser" => $retour->data
];
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

$params = new \stdClass();
$params->label = "infosUser";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);