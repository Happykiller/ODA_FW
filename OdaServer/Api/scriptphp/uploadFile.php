<?php
namespace Oda;
//--------------------------------------------------------------------------
//Header
require("../php/header.php");

//--------------------------------------------------------------------------
//Build the interface
$params = new SimpleObject\OdaPrepareInterface();
$params->interface = "API/scriptphp/uploadFile";
$params->arrayInput = array("dossier","nom");
$params->arrayInputOpt = array("replace" => true);
$ODA_INTERFACE = new OdaLibInterface($params);

//--------------------------------------------------------------------------
$config = OdaConfig::getInstance();

//init retour
$retour_array = array(
    'code' => null,
    'message' => null
);

if(is_null($config->resourcesPath)){
    $ODA_INTERFACE->dieInError("Upload impossible, path resource undefined.");
}else{
    foreach ($_FILES as $key => $value){
        //------------------------------------
        //FORMAT

        //Init traitement
        $fichier = basename($_FILES[$key]['name']);
        //On formate le nom du fichier ici...
        $fichier_format = strtr($fichier, 
             'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ$&#%!§', 
             'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy------');
        $fichier_format = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier_format);
        $fichier_format = addslashes($fichier_format);
        $taille_maxi = 500000;
        $taille = filesize($_FILES[$key]['tmp_name']);
        $extensions = array('.jpg','.png','.txt','.doc','.docx','.xls','.xlsx','.msg','.pdf');
        $extension = strrchr($_FILES[$key]['name'], '.'); 
        $extension = strtolower($extension);

        $path = "";
        //si path specifique pour les datas (openshift)
        $dossier = $ODA_INTERFACE->inputs["dossier"];
        if(isset($config->resourcesPath)){
            if($ODA_INTERFACE->inputs["nom"] != ""){
                $path = $config->resourcesPath . $dossier . $ODA_INTERFACE->inputs["nom"];
            }else{
                $path = $config->resourcesPath . $dossier . $fichier_format;
            }
        //sinon root
        }else{
            if($ODA_INTERFACE->inputs["nom"] != ""){
                $path = "../".$dossier . $ODA_INTERFACE->inputs["nom"];
            }else{
                $path = "../".$dossier . $fichier_format;
            }
        }

        //------------------------------------
        //TEST

        //Vérification extension
        if($path == "") //Si l'extension n'est pas dans le tableau
        {
            $retour_array['code'] = 'ko';
            $retour_array['message'] = 'Problème avec le path (vide).';
        }

        //Vérification extension
        if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
        {
            $retour_array['code'] = 'ko';
            $retour_array['message'] = 'Mauvais format de fichier (.jpg,.png,.txt,.doc,.docx,.xls,.xlsx,.msg,.pdf).';
        }

        //Vérification taille
        if($taille>$taille_maxi)
        {
            $retour_array['code'] = 'ko';
            $retour_array['message'] = 'Le fichier est trop gros 500ko max.'; 
        }

        //Vérification existance
        if(file_exists($path)) {
            if($ODA_INTERFACE->inputs["replace"] == true){
                $now = new \DateTime();
                $new = $path.".old-".$now->format('YmdHis');
                rename($path, $new);
            }else{
                $retour_array['code'] = 'ko';
                $retour_array['message'] = 'Le fichier existe déjà.'; 
            }
        }

        //------------------------------------
        //EXEC
        if(!isset($retour_array['code'])) //S'il n'y a pas d'erreur, on upload
        {
            if(move_uploaded_file($_FILES[$key]['tmp_name'], $path)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
            {
               $retour_array['code'] = 'ok';
               $details_array = array('message'=>'Upload effectue avec succes.','path'=>$path,'dossier'=>$dossier,'fichier'=>$fichier_format,'taille_maxi'=>$taille_maxi,'taille'=>$taille,'extensions'=>$extensions,'extension'=>$extension);
               $retour_array['message'] = $details_array; 
            } else {
                $retour_array['code'] = 'ko';
                $details_array = array('message'=>'Echec de l upload.','path'=>$path,'dossier'=>$dossier,'fichier'=>$fichier_format,'taille_maxi'=>$taille_maxi,'taille'=>$taille,'extensions'=>$extensions,'extension'=>$extension);
                $retour_array['message'] = $details_array;
            }
        }

        //------------------------------------
        $params = new \stdClass();
        $params->label = "resultat_".$key;
        $params->value = $retour_array;
        $ODA_INTERFACE->addDataStr($params);
    }
}