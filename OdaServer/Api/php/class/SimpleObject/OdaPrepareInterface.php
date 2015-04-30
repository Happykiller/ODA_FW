<?php
namespace Oda\SimpleObject;

class OdaPrepareInterface {
    /**
     * Mode debug, false by default
     * 
     * @var boolean 
     */
    public $modeDebug = false;
    /**
     * Public ou privé (clé obligatoire), true by default
     * 
     * @var boolean 
     */
    public $modePublic = true;
    /**
     * Mode de sortie text,json,xml,csv, json by default
     * 
     * Pour le xml et csv, data doit contenir qu'un est unique array.
     * 
     * @var string 
     */
    public $modeSortie = "json";
    /**
     * The list of input mandatory
     * 
     * @example array('param_name' => null)
     * @var array 
     */
    public $arrayInput = array();
    /**
     * The list of input optional
     * 
     * @example array('option' => 'defaultValue')
     * @var array 
     */
    public $arrayInputOpt = array();
    /**
     * The name of file output
     * 
     * @var string 
     */
    public $fileName = "";
}