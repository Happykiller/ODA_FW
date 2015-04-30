<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/scriptphp/send_mail";
$params->arrayInput = array("email_mails_dest","message_html", "sujet");
$params->arrayInputOpt = array(
    "email_mail_ori" => "oda-service-mail@oda.com",
    "email_labelle_ori" => "ODA Service Mail",
    "email_mail_reply" => null,
    "email_labelle_reply" => null,
    "email_mails_copy" => null,
    "email_mails_cache" => null,
    "message_txt" => "HTML not supported"
);
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
// API/scriptphp/send_mail.php?milis=123450&email_mail_ori=admin@mail.com&email_labelle_ori=ServiceMailOda&email_mail_reply=admin@mail.com&email_labelle_reply=ServiceMailOda&email_mails_dest=fabrice.rosito@gmail.com&email_mails_copy=fabrice.rosito@gmail.com&email_mails_cache=fabrice.rosito@gmail.com&message_txt=Anomalie avec le support du HTML.&message_html=<html><head></head><body><b>Merci</b> de repondre à ce mail en moins de 37min</body></html>&sujet=Hey mon ami !

//--------------------------------------------------------------------------
$config = OdaConfig::getInstance();

if($config->MAILGUN->isOK()){
    $result = OdaLib::sendMailGun($ODA_INTERFACE->inputs);
}else{
    $result = OdaLib::sendMail($ODA_INTERFACE->inputs);
}

//--------------------------------------------------------------------------
$params = new \stdClass();
$params->label = "resultat";
$params->value = $result;
$ODA_INTERFACE->addDataStr($params);