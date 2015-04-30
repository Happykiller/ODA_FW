<?php
namespace Oda;
use \Exception;
use \stdClass;
/**
 * LIBODA Librairy - main class
 *
 * Tool
 *
 * @author  Fabrice Rosito <rosito.fabrice@gmail.com>
 * @version 0.150127
 */
class OdaLibInterface {
    const STATE_INIT = 001;
    const STATE_CONSTRUCT = 002;
    const STATE_READY = 003;
    const STATE_ERROR = 004;
    const STATE_FINISH_DONE = 005;
    
    /**
     * Content of config.php object $OdaConfig
     * 
     * @var OdaConfig 
     */
    protected static $config;
    /**
     * All details of the interface
     * 
     * @var OdaPrepareInterface 
     */
    protected $params;
    /**
     * The bd engine
     * 
     * @var OdaLibBd 
     */
    public $BD_ENGINE;
    /**
     * The auth engine
     * 
     * @var OdaLibBd 
     */
    protected $BD_AUTH;
    /**
     * The Oda Key
     * 
     * @var string 
     */
    protected $keyAuth;
    /**
     * The details of output
     * 
     * @var OdaRetourInterface 
     */
    protected $object_retour;
    /**
     * The url for template
     * 
     * @var string 
     */
    public $urlTest;
    /**
     * The list of inputs
     * 
     * @var array 
     */
    public $inputs = array();
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
     * The name of file output
     * 
     * @var string 
     */
    public $fileName = "";
    /**
     * @var int 
     */
    protected $startMicro = 0;
    
    
    /**
     * @param type \Oda\OdaPrepareInterface
     * @return \Oda\OdaLibInterface
     */
    public function __construct(SimpleObject\OdaPrepareInterface $params){
        try {
            $this->params = $params;
            $this->modeDebug = $params->modeDebug;
            $this->modePublic = $params->modePublic;
            $this->modeSortie = $params->modeSortie;
            $this->fileName = $params->fileName;
            $this->object_retour = new SimpleObject\OdaRetourInterface();
            
            self::$config = SimpleObject\OdaConfig::getInstance();
            
            self::$config ->isOK();
            
            $params_bd = new stdClass();
            $params_bd->bd_conf = self::$config->BD_ENGINE;
            $params_bd->modeDebug = $this->modeDebug;
            $this->BD_ENGINE = new OdaLibBd($params_bd);
            
            if(is_null(self::$config->BD_AUTH->base)){
                $this->BD_AUTH = $this->BD_ENGINE;
            }else{
                $params_bd = new stdClass();
                $params_bd->bd_conf = self::$config->BD_AUTH;
                $params_bd->modeDebug = $this->modeDebug;
                $this->BD_AUTH = new OdaLibBd($params_bd);
            }
            
            $this->object_retour->statut = self::STATE_CONSTRUCT;
            
            $this->inputs = $this->recupInputs($params->arrayInput, $params->arrayInputOpt);
            
            $this->urlTest = $this->getUrlTest();
            
            $this->_initTransaction();
            
            $this->_checkKey();
            
            $this->object_retour->statut = self::STATE_READY;
            
            return $this;
        } catch (Exception $ex){
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
     * Destructor
     *
     * @access public
     * @return null
     */
    public function __destruct(){
        if($this->object_retour->statut != self::STATE_ERROR){
            $this->object_retour->statut = self::STATE_FINISH_DONE;
        }
        $strSorti = "";
        
        $fin = new SimpleObject\OdaDate();
        $this->object_retour->metro["ODAEnd"] = $fin->getDateTimeWithMili();
        
        $endMicro = SimpleObject\OdaDate::getMicro();

        $duree = $endMicro - $this->startMicro;
        $this->object_retour->metro["ODADuree"] = $duree;
        
        //choix du traitement
        switch ($this->modeSortie) {
            case "text":
                $strSorti = OdaLib::fomatage_text($this->object_retour);
                break;
            case "json":
                $strSorti = OdaLib::fomatage_json($this->object_retour);
                break;
            case "xml":
                $strSorti = OdaLib::fomatage_xml($this->object_retour->data);
                break;
            case "csv":
                $strSorti = OdaLib::fomatage_csv($this->object_retour->data);
                break;
            default:
               $strSorti = OdaLib::fomatage_text($this->object_retour);
               break;
        }
        
        if(!is_null($this->BD_ENGINE)){
            $this->_finishTransaction($strSorti, $fin);
        }
        
        if(!empty($this->fileName)&&(!empty($this->modeSortie))){
            header("Content-type: text/".$this->modeSortie."; charset=utf-8");
            header("Content-Disposition: attachment; filename=".$this->fileName.".".$this->modeSortie);
            header("Pragma: no-cache");
            header("Expires: 0");
            echo "\xEF\xBB\xBF"; // UTF-8 BOM
            echo $strSorti;
        } else {
            echo $strSorti;
        }
    }
    /*
     * @return array
     */
    protected function recupInputs($p_arrayIn, $p_arrayInOpt=array()) {
        try {
            $arrayOut = array ();
            $strError = null;

            //recup les gets
            $arrayGetAll = array ();
            if(isset($_GET)) {
                foreach($_GET as $key=>$val) {
                    $arrayGetAll[$key] = $val;
                }
            }

            //recup les posts
            $arrayPostAll = array ();
            if(isset($_POST)) {
                foreach($_POST as $key=>$val) {
                    $arrayPostAll[$key] = $val;
                }
            }

            //On init les inputs du framework
            if(isset($arrayPostAll["keyAuthODA"])){
                $arrayOut["keyAuthODA"] = $arrayPostAll["keyAuthODA"];
            }else if(isset($arrayGetAll["keyAuthODA"])){
                $arrayOut["keyAuthODA"] = $arrayGetAll["keyAuthODA"];
            }else{
                $arrayOut["keyAuthODA"] = null;
            }

            if(isset($arrayPostAll["ODAFileType"])){
                $arrayOut["ODAFileType"] = $arrayPostAll["ODAFileType"];
            }else if(isset($arrayGetAll["ODAFileType"])){
                $arrayOut["ODAFileType"] = $arrayGetAll["ODAFileType"];
            }else{
                $arrayOut["ODAFileType"] = null;
            }

            if(isset($arrayPostAll["ODAFileName"])){
                $arrayOut["ODAFileName"] = $arrayPostAll["ODAFileName"];
            }else if(isset($arrayGetAll["ODAFileName"])){
                $arrayOut["ODAFileName"] = $arrayGetAll["ODAFileName"];
            }else{
                $arrayOut["ODAFileName"] = null;
            }

            //on recupére les inputs déclarés en option
            foreach($p_arrayInOpt as $key=>$val) {
                if(isset($_POST[$key])){
                    $arrayOut[$key] = $arrayPostAll[$key];
                }else if(isset($_GET[$key])){
                    $arrayOut[$key] = $arrayGetAll[$key];
                }else{
                    $arrayOut[$key] = $val;
                }
            }

            //on recupére les inputs déclarés
            foreach($p_arrayIn as $val) {
                if(isset($_POST[$val])){
                    $arrayOut[$val] = $arrayPostAll[$val];
                }else if(isset($_GET[$val])){
                    $arrayOut[$val] = $arrayGetAll[$val];
                } else {
                    $arrayOut[$val] = null;
                    $strError .= $val.", ";
                }
            }
            
            if(!empty($arrayOut["ODAFileName"])){
                $this->fileName = $arrayOut["ODAFileName"];
            }
            
            if(!empty($arrayOut["ODAFileType"])){
                $this->modeSortie = $arrayOut["ODAFileType"];
            }
            
            if(!empty($arrayOut["keyAuthODA"])){
                $this->keyAuth = $arrayOut["keyAuthODA"];
            }
            
            //Erreur dans les inputs obligatoire
            if($strError != null){
                $this->inputs = $arrayOut;
                $params = new stdClass();
                $params->class = __CLASS__;
                $params->function = __FUNCTION__;
                $params->message = "Field(s) missing : ".$strError.'ex : '.$this->getUrlTest();
                throw new SimpleObject\OdaException($params);
            }
            
            return $arrayOut; 
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /*
     * @return string
     */
    public function getUrlTest(){
        try {
            $SCRIPT_NAME = $_SERVER["SCRIPT_NAME"];
            $tabName = explode("/",$SCRIPT_NAME);
            $SCRIPT_NAME = str_replace("/".$tabName[1]."/", "", $SCRIPT_NAME);
            
            $strUrl = self::$config->domaine.$SCRIPT_NAME."?milis=". SimpleObject\OdaDate::getMicro();
            
            foreach ($this->inputs as $key => $value){
                if(($key != 'keyAuthODA')&&($key != 'ODAFileName')&&($key != 'ODAFileType')){
                    if(is_null($value)||($value == '')){
                        $value = 'xx';
                    }
                    $strUrl .= "&" . $key . "=" . $value;
                }
            }
            
            return $strUrl;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
     * To add an object in data output
     * 
     * $p_params : 
     * - OdaRetourReqSql retourSql
     * - string label
     * 
     * @param stdClass p_params
     */
    public function addDataReqSQL($p_params){
        try {
            if($p_params->retourSql->strStatut!=OdaLibBd::SQL_STATUT_FINISH_OK){
                $this->object_retour->strErreur = $p_params->retourSql->strErreur;
                $this->object_retour->statut = self::STATE_ERROR;
                die();
            }else{
                if(isset($p_params->label)){
                    $this->object_retour->data[$p_params->label] = $p_params->retourSql->data;
                }else{
                    $this->object_retour->data = $p_params->retourSql->data;
                }
            }
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    
    /**
     * To add an object in data output
     * 
     * $p_params : 
     * - string value
     * - string label
     * 
     * @param stdClass p_params
     */
    public function addDataObject($p_params){
        try {
            if(isset($p_params->label)){
                $this->object_retour->data[$p_params->label] = $p_params->value;
            }else{
                $this->object_retour->data = $p_params;
            }
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
     * Attetion, not handle error from the OdaRetourSQL.
     * 
     * $p_params : 
     * - string value
     * - string label
     * 
     * or
     * 
     * $p_params  = string
     * 
     * @param stdClass|string  p_params
     */
    public function addDataStr($p_params){
        try {
            if(is_object($p_params)){
                if(is_object($p_params->value)){
                    $this->object_retour->strErreur = "The value is not a string.";
                    $this->object_retour->statut = self::STATE_ERROR;
                    die();
                }

                if(isset($p_params->label)){
                    $this->object_retour->data[$p_params->label] = $p_params->value;
                }else{
                    $this->object_retour->data = $p_params->value;
                }
            }else{
                $this->object_retour->data = $p_params;
            }
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
     * initTransaction
     * @return int
     */
    protected function _initTransaction () {
        try {
            $debut = new SimpleObject\OdaDate();
            $this->startMicro = SimpleObject\OdaDate::getMicro();
            $this->object_retour->metro["ODABegin"] = $debut->getDateTimeWithMili();
            
            $params = new SimpleObject\OdaPrepareReqSql();
            $params->sql = "INSERT INTO  `api_tab_transaction` (
                `id` ,
                `type` ,
                `statut` ,
                `input` ,
                `output` ,
                `debut` ,
                `fin`
                )
                VALUES (
                NULL ,  :type,  'debut',  :input,  '',  :debut,  ''
                )
            ;";
            $strJsonInput = OdaLib::fomatage_json($this->inputs);
            $params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
            $params->bindsValue = [
                "type" => [ "value" => $_SERVER["SCRIPT_FILENAME"]]
                , "input" => [ "value" => $strJsonInput]
                , "debut" => [ "value" => $debut->getDateTimeWithMili()]
            ];
            $retour = $this->BD_ENGINE->reqODASQL($params);
            $this->object_retour->id_transaction = $retour->data;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
     * finishTransaction
     */
    protected function _finishTransaction($p_strSorti, $fin){
        try {
            $params = new SimpleObject\OdaPrepareReqSql();
            $params->sql = "UPDATE `api_tab_transaction` 
                SET `output` = :strSort
                    , `statut` = 'output'
                    , `fin` = :fin
                WHERE `id` = :idTransaction
            ;";
            $params->typeSQL = OdaLibBd::SQL_SCRIPT;
            $params->bindsValue = [
                "idTransaction" => [ "value" => $this->object_retour->id_transaction]
                , "strSort" => [ "value" => $p_strSorti]
                , "fin" => [ "value" => $fin->getDateTimeWithMili()]
            ];
            $retour = $this->BD_ENGINE->reqODASQL($params);
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
     * checkKey
     * @param boolean $p_modePublic
     */
    protected function _checkKey(){
        try {
            if(!$this->modePublic){
                if($this->keyAuth == '') {
                    $this->object_retour->strErreur = "Key auth empty.";
                    $this->object_retour->statut = self::STATE_ERROR;
                    die();
                }else{
                    $params = new SimpleObject\OdaPrepareReqSql();
                    $params->sql = "Select *
                        , IF(a.`periodeValideMinute` = 0, true, IF(((a.`dateCreation` + INTERVAL a.`periodeValideMinute` MINUTE) < NOW()), false, true)) as 'ajour'
                        from `api_tab_session` a
                        WHERE 1=1
                        AND a.`key` = '".$this->keyAuth."'
                    ;";
                    $params->typeSQL = OdaLibBd::SQL_GET_ONE;
                    $retour = $this->BD_AUTH->reqODASQL($params);

                    if(!$retour->data){
                        $this->object_retour->strErreur = 'Key auth invalid.';
                        $this->object_retour->statut = self::STATE_ERROR;
                        die();
                    }else{
                        if($retour->data->ajour != 1){
                            $this->object_retour->strErreur = 'Session expired.';
                            $this->object_retour->statut = self::STATE_ERROR;
                            die();
                        }
                    }
                }
            }
        } catch ( Exception $ex ) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
     * checkSession
     * @param array $p_params
     * @return boolean
     */
    public function checkSession($p_params){
        try {
            $params = new SimpleObject\OdaPrepareReqSql();
            $params->sql = "SELECT true as 'check'
                FROM `api_tab_session` a
                WHERE 1=1
                AND a.`key` = :key
                AND a.`datas` like '%\"code_user\":\"".$p_params["code_user"]."\"%'
                AND (a.`dateCreation` + INTERVAL a.`periodeValideMinute` MINUTE) > NOW()
            ;";
            $params->bindsValue = [
                "key" => $p_params["key"]
            ];
            $params->typeSQL = OdaLibBd::SQL_GET_ONE;
            $retour = $this->BD_AUTH->reqODASQL($params);

            return $retour->nombre;
        } catch ( Exception $ex ) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
     * deleteSession
     * @param string $p_params
     * @return boolean
     */
    public function deleteSession($p_params){
        try {
            $params = new SimpleObject\OdaPrepareReqSql();
            $params->sql = "DELETE
                FROM `api_tab_session`
                WHERE 1=1
                AND `key` = :key
            ;";
            $params->bindsValue = [
                "key" => $p_params
            ];
            $params->typeSQL = OdaLibBd::SQL_SCRIPT;
            $retour = $this->BD_AUTH->reqODASQL($params);

            return $retour->nombre;
        } catch ( Exception $ex ) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
     * buildSession
     * @param array $p_params
     * @return string
     */
    public function buildSession($p_params){
        try {
            $v_code_user = $p_params["code_user"];
            $v_key = "";

            //Detruit les veilles clés
            $params = new SimpleObject\OdaPrepareReqSql();
            $params->sql = "DELETE FROM `api_tab_session`
                WHERE 1=1
                AND `datas` like '%\"code_user\":\"".$v_code_user."\"%'
                AND (`dateCreation` + INTERVAL `periodeValideMinute` MINUTE) < NOW()
                AND `periodeValideMinute` != 0
            ;";
            $params->typeSQL = OdaLibBd::SQL_SCRIPT;
            $retour = $this->BD_AUTH->reqODASQL($params);
            
            //Vérifie la présence d'une clé
            $params = new SimpleObject\OdaPrepareReqSql();
            $params->sql = "SELECT *
                FROM `api_tab_session` a
                WHERE 1=1
                AND a.`datas` like '%\"code_user\":\"".$v_code_user."\"%'
                AND (a.`dateCreation` + INTERVAL a.`periodeValideMinute` MINUTE) > NOW()
            ;";
            $params->typeSQL = OdaLibBd::SQL_GET_ONE;
            $retour = $this->BD_AUTH->reqODASQL($params);
            
            if($retour->data){
                $v_key = $retour->data->key;
            }else{
                //Check log pass
                $params = new SimpleObject\OdaPrepareReqSql();
                $params->sql = "SELECT *
                    FROM `api_tab_utilisateurs` a
                    WHERE 1=1
                    AND a.`code_user` = '".$v_code_user."'
                    AND a.`password` = '".$p_params["password"]."'
                ;";
                $params->typeSQL = OdaLibBd::SQL_GET_ONE;
                $retour = $this->BD_AUTH->reqODASQL($params);
                
                if($retour->data){
                    //Construit une nouvelle clé
                    $v_strDate = \date('YmdHis');
                    $v_key = \md5($v_code_user."_".$v_strDate);
                    $json = new stdClass();
                    $json->code_user = $v_code_user;
                    $json->date = $v_strDate;

                    $params = new SimpleObject\OdaPrepareReqSql();
                    $params->sql = "INSERT INTO `api_tab_session`(
                            `id` ,
                            `key` ,
                            `datas` ,
                            `dateCreation` ,
                            `periodeValideMinute`
                        )
                        VALUES (
                            NULL , '".$v_key."',  '".\json_encode($json)."',  NOW(), 720 
                        )
                    ;";
                    $params->typeSQL = OdaLibBd::SQL_INSERT_ONE;
                    $retour = $this->BD_ENGINE->reqODASQL($params);
                }else{
                    $this->object_retour->strErreur = 'Auth impossible.';
                    $this->object_retour->statut = self::STATE_ERROR;
                    die();
                }
            }

            return $v_key;
        } catch ( Exception $ex ) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    /**
    * @name getParameter
    * @param string $p_parameterName
    * @return string|int $parameterValue
    */
    public function getParameter ($p_parameterName) {
        try {
            $parameterValue = null;

            $params = new stdClass();
            $params->nameObj = "api_tab_parametres";
            $params->keyObj = ["param_name" => $p_parameterName];
            $retour = $this->BD_ENGINE->getSingleObject($params);

            switch ($retour->param_type) {
                case "varchar":
                    $parameterValue = $retour->param_value;
                    break;
                case "int":
                    $parameterValue = (int) $retour->param_value;
                    break;
                default:
                    $parameterValue = $retour->param_value;
                    break;
            }

           return $parameterValue;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
       }
    }
    
    /**
    * @name setParameter
    * @desc met à jour la valeur du param
    * @return boolean
    */
    function setParameter($p_param, $p_valeur) {
        try {
            $params = new \stdClass();
            $params->nameObj = "api_tab_parametres";
            $params->keyObj = ["param_name" => $p_param];
            $params->setObj = ["param_value" => $p_valeur];
            $id = $this->BD_ENGINE->setSingleObj($params);

            return $id;
        } catch (Exception $ex) {
            $this->object_retour->strErreur = $ex.'';
            $this->object_retour->statut = self::STATE_ERROR;
            die();
        }
    }
    
    /**
     * To die an interface
     * 
     * @param String $message
     */
    public function dieInError($message){
        $this->object_retour->strErreur = $message;
        $this->object_retour->statut = self::STATE_ERROR;
        die();
    }
}