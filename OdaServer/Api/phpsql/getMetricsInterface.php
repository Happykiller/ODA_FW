<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/getMetricsInterface";
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/getMetricsInterface.php?milis=123450&ctrl=ok
    
//--------------------------------------------------------------------------
$params = new SimpleObject\OdaPrepareReqSql();
$params->sql = "SELECT REPLACE(`type`,SUBSTRING_INDEX(`type`, '/', 4), '') as 'interface' 
    ,COUNT(`id`) as 'nb'
    ,COUNT(`id`) *  AVG(TIMEDIFF(`fin`,`debut`)) as 'cost'
    ,AVG(TIMEDIFF(`fin`,`debut`)) as 'average'
    ,MAX(TIMEDIFF(`fin`,`debut`)) as 'maxTime'
    ,MIN(TIMEDIFF(`fin`,`debut`)) as 'minTime'
    FROM  `api_tab_transaction` 
    WHERE 1=1
    AND `fin` != '0000-00-00 00:00:00'
    AND `fin` > NOW() - INTERVAL 7 DAY
    GROUP BY `type`
;";
$params->typeSQL = OdaLibBd::SQL_GET_ALL;
$retour = $ODA_INTERFACE->BD_ENGINE->reqODASQL($params);

//---------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "metrics";
$params->retourSql = $retour;
$ODA_INTERFACE->addDataReqSQL($params);