<?php
//Header établie la connection à la base $connection
require("../../API/php/header.php");

//Fonctions : Fonctions personnelles de l'instance
require("../../php/fonctions.php");

//Mode debug
$modeDebug = false;

//Public ou privé (clé obligatoire)
$modePublic = true;

//Mode de sortie text,json,xml,csv
//Pour le text utiliser $strSorti pour la sortie.
//Pour le json $object_retour sera decoder
//Pour le xml et csv $object_retour->data["resultat"]->data doit contenir qu'un est unique array.
$modeSortie = "json";

//Liens de test
// phpsql/test/ckeckCountBasique.php?milis=123450&ctrl=ok

//Définition des entrants
$arrayInput = array(
    "ctrl" => null
);

//Définition des entrants optionel
$arrayInputOpt = array(
    "option" => null
);

//Récupération des entrants
$HOW_INTERFACE->inputs = recupInput($arrayInput,$arrayInputOpt);

//Object retour minima
// $object_retour->strErreur string
// $object_retour->data  string
// $object_retour->statut  string
// $object_retour->id_transaction  string
// $object_retour->metro  string

//--------------------------------------------------------------------------
$params = new stdClass();
$params->connection = $connection;
$params->sql = "SELECT count(DISTINCT a.`nom`) as 'nb'
    FROM `tab_inventaire` a
    WHERE 1=1
    AND a.`mode` = 'Basique'
;";
$params->typeSQL = $liboda->SQL_GET_ONE;
$params->debug = $modeDebug;
$retour = $liboda->reqODASQL($params);
$object_retour->data["resultat"] = $retour->data;

//---------------------------------------------------------------------------

//Cloture de l'interface
require("../../API/php/footer.php");