<?php
require_once(__DIR__."/../API/php/class/SimpleObject/OdaConfig.php");
$config = \Oda\SimpleObject\OdaConfig::getInstance();
$config->domaine = "http://localhost/ODA_SANDBOX/back/";

//for bd engine
$config->BD_ENGINE->base = 'how';
$config->BD_ENGINE->mdp = 'pass';
$config->BD_ENGINE->prefixTable = 'how-';