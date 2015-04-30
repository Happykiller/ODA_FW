<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/phpsql/setParam";
$params->arrayInput = array("param_name","param_value");
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/phpsql/setParam.php?milis=123456789&param_name=nom_site&param_value=test

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->nameObj = "api_tab_parametres";
$params->keyObj = ["param_name" => $ODA_INTERFACE->inputs["param_name"]];
$params->setObj = ["param_value" => $ODA_INTERFACE->inputs["param_value"]];
$id = $ODA_INTERFACE->BD_ENGINE->setSingleObj($params);

//---------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultat";
$params->value = $id;
$ODA_INTERFACE->addDataStr($params);