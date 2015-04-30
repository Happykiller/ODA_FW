<?php
//Config : Les informations personnels de l'instance (log, pass, etc)
require_once(dirname(__FILE__)."/../include/config.php");

//API lib for ODA
require_once(dirname(__FILE__).'/../API/php/liboda.class.php');

//Old API lib for ODA
require_once(dirname(__FILE__)."/../API/php/fonctions.php");

//Project functions
require_once(dirname(__FILE__)."/../php/fonctions.php");

//API lib for ODA
$liboda = new LIBODA();

//--------------------------------------------------------------------------
// On transforme les résultats en tableaux d'objet
$retours = array();

//--------------------------------------------------------------------------
$retours[] = test("ckeckCountBasique",function() {
        global $domaine, $liboda;
    
        $params = new stdClass();
        $input = ["milis" => "123451","ctrl" => "ok"];
        $retourCallRest = $liboda->CallRest($domaine."phpsql/test/ckeckCountBasique.php", $params, $input);
        
        equal($retourCallRest->data->resultat->nb, "133", "On vérifie qu'il ne manque pas de carte de basique. (133)");
    }         
);

//--------------------------------------------------------------------------
//Out
$resultats = new stdClass();
$resultats->details = $retours;
$resultats->succes = 0;
$resultats->echec = 0;
$resultats->total = 0;
foreach($retours as $key => $value) {
    $resultats->succes += $value->succes;
    $resultats->echec += $value->echec;
    $resultats->total += $value->total;
 }

//--------------------------------------------------------------------------
$resultats_json = json_encode($resultats);

$strSorti = $resultats_json;

print_r($strSorti);