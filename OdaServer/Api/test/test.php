<?php
namespace Oda;
use stdClass;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/test";
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/test.php

//--------------------------------------------------------------------------
$config = OdaConfig::getInstance();


// On transforme les résultats en tableaux d'objet
$retours = array();

//--------------------------------------------------------------------------
//Technique part
$retours[] = OdaLib::test("get_string_between",function() {
        $v_test = OdaLib::get_string_between("01234", "1", "3");
        OdaLib::equal($v_test, "2", "Test OK : Passed!");
    }         
);

$retours[] = OdaLib::test("OP parameter",function() {
        global $ODA_INTERFACE, $config;
        $v_test = $ODA_INTERFACE->getParameter("maintenance");
        OdaLib::equal($v_test, "0", "Test OK : Passed!");
        
        $v_test = $ODA_INTERFACE->setParameter("maintenance",$v_test);
        OdaLib::equal($v_test, true, "Test OK : Passed!");
    }         
);

$retours[] = OdaLib::test("test_secu",function() {
        global $ODA_INTERFACE, $config;

        //---------------------------------------
        $params = new stdClass();
        $input = ["milis" => "123451","ctrl" => "ok","keyAuthODA" => "42c643cc44c593c5c2b4c5f6d40489dd"];
        $retourCallRest = OdaLib::CallRest($config->domaine."API/phpsql/tests/test_secu.php", $params, $input);
        
        OdaLib::equal($retourCallRest->data->resultat->param_type, "int", "Test OK : Passed! (avec key)");

        //---------------------------------------
        $params = new stdClass();
        $input = ["milis" => "123451","ctrl" => "ok","keyAuthODA" => "badkey"];
        $retourCallRest = OdaLib::CallRest($config->domaine."API/phpsql/tests/test_secu.php", $params, $input);
        
        OdaLib::equal($retourCallRest->strErreur, "Key auth invalid.", "Test KO : Passed! (key : caca)");

        //---------------------------------------
        $params = new stdClass();
        $input = ["milis" => "123451","ctrl" => "ok","keyAuthODA" => ""];
        $retourCallRest = OdaLib::CallRest($config->domaine."API/phpsql/tests/test_secu.php", $params, $input);
        
        OdaLib::equal($retourCallRest->strErreur, "Key auth empty.", "Test KO : Passed! (key : vide)");

        //---------------------------------------
        $params = new stdClass();
        $input = ["milis" => "123451","ctrl" => "ok"];
        $retourCallRest = OdaLib::CallRest($config->domaine."API/phpsql/tests/test_secu.php", $params, $input);
        
        OdaLib::equal($retourCallRest->strErreur, "Key auth empty.", "Test KO : Passed! (key : inexistante)");
    }         
);

//--------------------------------------------------------------------------
//System part
$retours[] = OdaLib::test("getAuth",function() {
        global $ODA_INTERFACE, $config;
        
        //---------------------------------------
        $params = new stdClass();
        $input = [
            "login" => "VIS",
            "mdp" =>  "VIS"
        ];
        $retourCallRest = OdaLib::CallRest($config->domaine."API/phpsql/getAuth.php", $params, $input);

        OdaLib::equal($retourCallRest->data->resultat->profile, 99, "Test OK : Passed! (Avec bon log, pass)");

        //---------------------------------------
        $params = new stdClass();
        $input = [
            "login" => "badlog",
            "mdp" =>  "badpass"
        ];
        $retourCallRest = OdaLib::CallRest($config->domaine."API/phpsql/getAuth.php", $params, $input);
        
        OdaLib::equal($retourCallRest->strErreur, "Auth impossible.", "Test KO : Passed! (Avec mauvais log, pass)");
    }         
);

$retours[] = OdaLib::test("getAuthInfo",function() {
        global $ODA_INTERFACE, $config;

        //---------------------------------------
        $params = new stdClass();
        $input = [
            "code_user" => "VIS"
        ];
        $retourCallRest = OdaLib::CallRest($config->domaine."API/phpsql/getAuthInfo.php", $params, $input);
        
        OdaLib::equal($retourCallRest->data->resultat->profile, 99, "Test OK : Passed! (Avec code user existant)");

        //---------------------------------------
        $params = new stdClass();
        $input = [
            "code_user" => "fdfdfd"
        ];
        $retourCallRest = OdaLib::CallRest($config->domaine."API/phpsql/getAuthInfo.php", $params, $input);
        
        OdaLib::equal($retourCallRest->data->resultat, false, "Test OK : Passed! (Avec code user inconnu)");
    }         
);

//--------------------------------------------------------------------------
//job part
$retours[] = OdaLib::test("getDetailsUser",function() {
        global $ODA_INTERFACE, $config;

        //---------------------------------------
        $params = new stdClass();
        $input = [
            "code_user" => "VIS",
            "profile" => "99"
        ];
        $retourCallRest = OdaLib::CallRest($config->domaine."API/phpsql/getDetailsUser.php", $params, $input);
        
        OdaLib::equal($retourCallRest->data->detailsUser->profile, 99, "Test OK : Passed! (Avec code user existant)");
    }         
);

$retours[] = OdaLib::test("getListMail",function() {
        global $ODA_INTERFACE, $config;

        //---------------------------------------
        $params = new stdClass();
        $input = [];
        $retourCallRest = OdaLib::CallRest($config->domaine."API/phpsql/getListMail.php", $params, $input);
        
        OdaLib::equal((count($retourCallRest->data->resultat) > 0), true, "Test OK : Passed! (".count($retourCallRest->data->resultat)." found)");
    }         
);

$retours[] = OdaLib::test("singleObjGet",function() {
        global $ODA_INTERFACE, $config;

        $params = new stdClass();
        $params->nameObj = "api_tab_parametres";
        $params->keyObj = ["param_name" => 'nom_site'];
        $params->debug = false;
        $parameter = $ODA_INTERFACE->BD_ENGINE->getSingleObject($params);
        OdaLib::equal(isset($parameter->param_value), true, "Test OK : Passed! ('".$parameter->param_value."' found)");
    }         
);

$retours[] = OdaLib::test("singleObjSet",function() {
        global $ODA_INTERFACE, $config;
        
        $params = new SimpleObject\OdaPrepareReqSql();
        $params->sql = "DELETE FROM `api_tab_parametres`
            WHERE 1=1
            AND `param_name` = 'varTestU'
        ;";
        $params->typeSQL = OdaLibBd::SQL_SCRIPT;
        $retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

        $params = new stdClass();
        $params->nameObj = "api_tab_parametres";
        $params->keyObj = ["param_name" => "varTestU"];
        $params->setObj = ["param_value" => "test"];
        $retour = $ODA_INTERFACE->BD_ENGINE->setSingleObj($params);
        OdaLib::equal(!is_null($retour), true, "Test OK (Create by set) : Passed! ('".$retour."' found)");
        
        $params = new stdClass();
        $params->nameObj = "api_tab_parametres";
        $params->keyObj = ["param_name" => "varTestU"];
        $params->setObj = ["param_value" => "test2"];
        $retour = $ODA_INTERFACE->BD_ENGINE->setSingleObj($params);
        OdaLib::equal(!is_null($retour), true, "Test OK (edit by set) : Passed! ('".$retour."' found)");
        
        $params = new SimpleObject\OdaPrepareReqSql();
        $params->sql = "DELETE FROM `api_tab_parametres`
            WHERE 1=1
            AND `param_name` = 'varTestU'
        ;";
        $params->typeSQL = OdaLibBd::SQL_SCRIPT;
        $retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);
    }         
);

$retours[] = OdaLib::test("getListutilisateurs",function() {
        global $ODA_INTERFACE, $config;

        //---------------------------------------
        $params = new stdClass();
        $input = ["indice_user" => 1];
        $retourCallRest = OdaLib::CallRest($config->domaine."API/phpsql/getListutilisateurs.php", $params, $input);
        
        OdaLib::equal(($retourCallRest->data->resultats->nombre), true, "Test OK : Passed! (".$retourCallRest->data->resultats->nombre." found)");
    }         
);

$retours[] = OdaLib::test("send_mail",function() {
        global $ODA_INTERFACE, $config;

        //---------------------------------------
        $params = new stdClass();
        $input = [
            "email_mails_dest" => "fabrice.rosito@gmail.com"
            , "message_html" => "<html><head></head><body><b>Merci</b> de répondre à ce mail en moins de 37min</body></html>"
            , "sujet" => "Hey mon ami !"
        ];
        $retourCallRest = OdaLib::CallRest($config->domaine."API/scriptphp/send_mail.php", $params, $input);
        
        OdaLib::equal(($retourCallRest->statut == "ok"), true, "Test OK : Passed! (id_transaction : ".$retourCallRest->id_transaction.")");
    }         
);

$retours[] = OdaLib::test("logTrace",function() {
        global $ODA_INTERFACE, $config;

        $retourCall = $ODA_INTERFACE->BD_ENGINE->logTrace(0,"testU:logTrace");
        
        OdaLib::equal(($retourCall > 0), true, "Test OK : Passed! (id : ".$retourCall.")");
    }         
);

//--------------------------------------------------------------------------
//Out
$resultats = new \stdClass();
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
$ODA_INTERFACE->addDataObject($resultats);