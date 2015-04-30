<?php
namespace Oda\SimpleObject;
class OdaRetourReqSql {
    /**
     * The code statut
     * @var int 
     */
    public $strStatut = \Oda\OdaLibBd::SQL_STATUT_INIT;
    /**
     * If a error appear
     * @var string 
     */
    public $strErreur = "";
    /**
     * The result of query
     * @var stdClass
     */
    public $data;
    /**
     * The number of result
     * @var int
     */
    public $nombre;
}
