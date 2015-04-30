<?php
namespace Oda\SimpleObject;
class OdaPrepareReqSql {
    /**
     * The sql query
     * @var string 
     */
    public $sql = "";
    /**
     * Bind values
     * @var array 
     */
    public $bindsValue = [];
    /**
     * Type of req, OdaLibBd::SQL_SCRIPT by default
     * @var int 
     */
    public $typeSQL = \Oda\OdaLibBd::SQL_SCRIPT;
    /**
     * PDO::FETCH_CLASS value
     * 
     * @var string 
     */
    public $className = "";
    /**
     * Mode debug, false by default
     * 
     * @var boolean 
     */
    public $debug = false;
}

