<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/insertLog";
$params->arrayInput = array("type","msg");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/insertLog.php?milis=123450&type=0&msg=wtf

//--------------------------------------------------------------------------
$id = $ODA_INTERFACE->BD_ENGINE->logTrace($ODA_INTERFACE->inputs["type"], addslashes($ODA_INTERFACE->inputs["msg"]));

//---------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultatInsert";
$params->value = $id;
$ODA_INTERFACE->addDataStr($params);