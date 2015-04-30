<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->arrayInput = array("code_user", "key");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/checkSession.php?milis=123450&code_user=FRO&key=e6fff655cb3121c08a8219497ba9358e

//--------------------------------------------------------------------------
$retour = $ODA_INTERFACE->checkSession($ODA_INTERFACE->inputs);

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->value = $retour;
$ODA_INTERFACE->addDataStr($params);
