<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/getMessages";
$params->arrayInput = array();
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/getMessages.php?milis=123450&ctrl=ok
    
//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "Select a.`actif`, a.`message`, b.`labelle` as 'profile', a.`niveau`, a.`date_expiration`, a.`code_user_creation`, a.`date_creation`
    FROM `api_tab_messages` a, `api_tab_rangs` b
    WHERE 1=1
    AND a.`profile` = b.`indice`
    ORDER BY a.`id` desc
    LIMIT 0, 10
;";
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//---------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "messages";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);