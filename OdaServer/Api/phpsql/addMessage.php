<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/addMessage";
$params->arrayInput = array("message","niveau","profile","date_expiration","code_user");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// addMessage.php?milis=123450&ctrl=ok&message=Hello&niveau=ALERT&profile=30&date_expiration=2014-01-26&code_user=FRO
    
//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "INSERT INTO `api_tab_messages`
    (`actif`, `message`, `profile`, `niveau`, `date_expiration`, `code_user_creation`, `date_creation`) 
    VALUES 
    ( 1 ,  :message, :profile, :niveau, :date_expiration, :code_user, NOW() )
;";
$params->bindsValue = [
    "message" => $ODA_INTERFACE->inputs["message"],
    "profile" => $ODA_INTERFACE->inputs["profile"],
    "niveau" => $ODA_INTERFACE->inputs["niveau"],
    "date_expiration" => $ODA_INTERFACE->inputs["date_expiration"],
    "code_user" => $ODA_INTERFACE->inputs["code_user"]
];
$params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);  

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultatInsert";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);

//---------------------------------------------------------------------------
