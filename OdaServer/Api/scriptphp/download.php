<?php
// scriptphp/download.php?milis=123456789&fic=../outils/page_script_outils.zip
if(isset($_GET["fic"])){
    $Fichier_a_telecharger=basename($_GET["fic"]);
    $chemin=dirname($_GET["fic"])."/";
    // reconnait l'extension pour que le téléchargement
    // corresponde au type de fichier afin d'éviter les erreurs de corruptions

    switch(strrchr(basename($Fichier_a_telecharger), "."))
    {
        case ".gz": $type = "application/x-gzip"; break;
        case ".tgz": $type = "application/x-gzip"; break;
        case ".zip": $type = "application/zip"; break;
        case ".pdf": $type = "application/pdf"; break;
        case ".png": $type = "image/png"; break;
        case ".gif": $type = "image/gif"; break;
        case ".jpg": $type = "image/jpeg"; break;
        case ".txt": $type = "text/plain"; break;
        case ".htm": $type = "text/html"; break;
        case ".html": $type = "text/html"; break;
        default: $type = "application/octet-stream"; break;
    }

    header("Content-disposition: attachment; filename=$Fichier_a_telecharger");
    header("Content-Type: application/force-download");
    header("Content-Transfer-Encoding: $type\n"); // ne pas enlever le \n
    header("Content-Length: ".filesize($chemin . $Fichier_a_telecharger));
    header("Pragma: no-cache");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
    header("Expires: 0");
    readfile($chemin . $Fichier_a_telecharger);
}