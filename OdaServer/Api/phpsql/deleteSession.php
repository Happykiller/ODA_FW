<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->arrayInput = array("key");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/deleteSession.php?key=e6fff655cb3121c08a8219497ba9358e

//--------------------------------------------------------------------------
$retour = $ODA_INTERFACE->deleteSession($ODA_INTERFACE->inputs["key"]);

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->value = $retour;
$ODA_INTERFACE->addDataStr($params);
