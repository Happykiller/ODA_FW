<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/setMessagesLus";
$params->arrayInput = array("code_user");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/setMessagesLus.php?milis=123450&ctrl=ok&code_user=VIS
    
//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "INSERT INTO `api_tab_messages_lus`(`code_user`, `id_message`, `datelu`)
    Select '".$ODA_INTERFACE->inputs["code_user"]."', a.`id` , NOW()
    FROM `api_tab_messages` a
    WHERE 1=1
    AND NOT EXISTS (
        SELECT 1
        FROM  `api_tab_messages_lus` b
        WHERE 1=1
        AND a.`id` = b.`id_message`
        AND b.`code_user` = :code_user
    )
    AND a.`profile` >= (
        SELECT c.`profile`
        FROM `api_tab_utilisateurs` c
        WHERE 1=1
        AND c.`code_user` = :code_user
    )
    AND IF(a.`date_expiration` != '0000-00-00', a.`date_expiration` > NOW(), (a.`date_creation` + INTERVAL 7 DAY) > NOW())
    ORDER BY a.`id` desc
    LIMIT 0, 10
;";
$params->bindsValue = [
    "code_user" => $ODA_INTERFACE->inputs["code_user"]
];
$params->typeSQL = OdaLibBd::SQL_SCRIPT;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultat";
$params->value = $retour->nombre;
$ODA_INTERFACE->addDataStr($params);