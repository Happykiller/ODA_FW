<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/maintenanceDb";
$params->arrayInput = array("exec");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/maintenanceDb.php?milis=123450&exec=false

//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "SELECT 'api_tab_session' as 'table', COUNT(*) as 'nb'
FROM `api_tab_session`
UNION
SELECT 'api_tab_transaction' as 'table', COUNT(*) as 'nb'
FROM `api_tab_transaction`
UNION
SELECT 'api_tab_log' as 'table', COUNT(*) as 'nb'
FROM `api_tab_log`
;";
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$v_resultats = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

$nb_api_tab_session = intval($v_resultats->data->data[0]->nb);
$nb_api_tab_transaction = intval($v_resultats->data->data[1]->nb);
$nb_api_tab_log = intval($v_resultats->data->data[2]->nb);

$params = new \stdClass();
$params->label = "resultat";
$params->retourSql = $v_resultats;
$ODA_INTERFACE->addDataReqSQL($params);

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "exec";
$params->value = $ODA_INTERFACE->inputs["exec"];
$ODA_INTERFACE->addDataStr($params);

//--------------------------------------------------------------------------
$array_purges = array();
if($ODA_INTERFACE->inputs["exec"] == "true"){
    //--------------------------------------------------------------------------
    // Purge api_tab_session
    $obj_purge = new stdClass();
    $obj_purge->table = "api_tab_session";
    $obj_purge->nb = 0;
    $obj_purge->statut = "none";
    if($nb_api_tab_session > 1000){
        $obj_purge->statut = "init";

        $params = new SimpleObject\OdaPrepareReqSql();
        $params->sql = "DELETE
            FROM `api_tab_session`
            WHERE 1=1
            AND `periodeValideMinute` != 0
            AND `dateCreation` < date_sub(now(), interval 1 month)
            AND NOW() > date_add(a.`dateCreation`, interval + `periodeValideMinute` minute)
        ;";
        $params->typeSQL = OdaLibBd::SQL_SCRIPT;
        $retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);
        $obj_purge->nb = $retour->nombre;
        $obj_purge->statut = "done";
    }
    $array_purges[] = $obj_purge;

    //--------------------------------------------------------------------------
    // Purge api_tab_transaction
    $obj_purge = new stdClass();
    $obj_purge->table = "api_tab_transaction";
    $obj_purge->nb = 0;
    $obj_purge->statut = "none";
    if($nb_api_tab_transaction > 1000){
        $obj_purge->statut = "init";

        $params = new SimpleObject\OdaPrepareReqSql();
        $params->sql = "DELETE
            FROM `api_tab_transaction`
            WHERE 1=1
            AND `debut` < date_sub(now(), interval 7 day)
        ;";
        $params->typeSQL = OdaLibBd::SQL_SCRIPT;
        $retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);
        $obj_purge->nb = $retour->nombre;
        $obj_purge->statut = "done";
    }
    $array_purges[] = $obj_purge;

    //--------------------------------------------------------------------------
    // Purge api_tab_log
    $obj_purge = new stdClass();
    $obj_purge->table = "api_tab_log";
    $obj_purge->nb = 0;
    $obj_purge->statut = "none";
    if($nb_api_tab_log > 1000){
        $obj_purge->statut = "init";

        $params = new SimpleObject\OdaPrepareReqSql();
        $params->sql = "DELETE
            FROM `api_tab_log`
            WHERE 1=1
            AND `dateTime` < date_sub(now(), interval 7 day)
        ;";
        $params->typeSQL = OdaLibBd::SQL_SCRIPT;
        $retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);
        $obj_purge->nb = $retour->nombre;
        $obj_purge->statut = "done";
    }
    $array_purges[] = $obj_purge;
}
//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "purges";
$params->value = $array_purges;
$ODA_INTERFACE->addDataStr($params);