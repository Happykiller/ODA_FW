<?php
namespace Oda;
use \PDO;
use \Exception;
use \stdClass;
/**
 * LIBODA Librairy - main class
 *
 * Tool
 *
 * @author  Fabrice Rosito <rosito.fabrice@gmail.com>
 * @version 0.150128
 */
class OdaLibBd {    
    const SQL_GET_ONE = 001;
    const SQL_GET_ALL = 002;
    const SQL_INSERT_ONE = 003;
    const SQL_SCRIPT = 004;

    const SQL_STATUT_INIT = 001;
    const SQL_STATUT_RUN = 002;
    const SQL_STATUT_FAIL = 003;
    const SQL_STATUT_FINISH_KO = 004;
    const SQL_STATUT_FINISH_OK = 005;

    const ERROR_SQL_NOT_DEFINE = 001;
    const ERROR_SQL_SYNTAX = 002;
    const ERROR_SQL_TOO_MUCH_RECORD = 003;
    
    /**
     * Content of config.php object $OdaConfig
     * 
     * @var OdaConnection 
     */
    private static $config;
    /**
     * Mode debug, false by default
     * 
     * @var boolean 
     */
    public $modeDebug = false;
    /**
     * The bd
     * 
     * @var OdaLibBd 
     */
    private $connection;
    /**
     * @var array
     */
    private $listTables;


    /**
     * class constructor
     *
     * @param stdClass $p_params
     * @return OdaDate $this
     */
    public function __construct($p_params){
        $params_attempt = new SimpleObject\OdaPrepareBd();
        $params_attempt->modeDebug = $this->modeDebug;
        try {
            $params = (object) array_merge((array) $params_attempt, (array) $p_params);
            $this->modeDebug = $params->modeDebug;
            if(!is_a($params->bd_conf, 'Oda\SimpleObject\OdaConnection')){
                $params = new stdClass();
                $params->class = __CLASS__;
                $params->function = __FUNCTION__;
                $params->message = "Conf is not a OdaConnection.";
                throw new SimpleObject\OdaException($params);
            }
            
            $params->bd_conf->isOk();
            
            self::$config = $params->bd_conf;
            
            $this->connection = $this->buildConnection(self::$config);
            
            $this->listTables = $this->getListTables();
            
            return $this;
        }  catch (Exception $ex){
           $msg = $ex->getMessage();
           Throw new Exception('Erreur dans '.__CLASS__.' : '.$msg);
        }
    }
    /**
     * Destructor
     *
     * @access public
     * @return null
     */
    public function __destruct(){
        
    }
    /*
     * buildConnection
     *
     * @access private
     * @return null
     */
    private function buildConnection(SimpleObject\OdaConnection $BD_CONF){
        try {
            if(is_null($BD_CONF->login)){
                $BD_CONF->login = $BD_CONF->base;
            }
            switch ($BD_CONF->type) {
                case "mysql":
                    $v_dns = 'mysql:host='.$BD_CONF->host.';port='.$BD_CONF->port.';dbname='.$BD_CONF->base;
                    $v_connection = new PDO($v_dns, $BD_CONF->login, $BD_CONF->mdp );

                    //Test
                    $strSql = "Select 1;";
                    $result = $v_connection->query($strSql);
                    if(!$result){
                        $params = new stdClass();
                        $params->class = __CLASS__;
                        $params->function = __FUNCTION__;
                        $params->message = "Query test fail : ".$v_connection->errorInfo();
                        throw new SimpleObject\OdaException($params);
                    }
                    break;
                case "oracle":
                    $lien_base =
                    "oci:dbname=(DESCRIPTION =
                        (ADDRESS_LIST =
                                (ADDRESS =
                                        (PROTOCOL = TCP)
                                        (Host = ".$BD_CONF->host .")
                                        (Port = ".$BD_CONF->port."))
                        )
                        (CONNECT_DATA =
                                (SERVICE_NAME = ".$BD_CONF->base.")
                        )
                    )";
                    $v_connection = new PDO($lien_base, $BD_CONF->login, $BD_CONF->mdr); 
                    break;
                default:
                    $params = new stdClass();
                    $params->class = __CLASS__;
                    $params->function = __FUNCTION__;
                    $params->message = "Connection impossible ".$BD_CONF->type." is unknow.";
                    throw new SimpleObject\OdaException($params);
            }
            
            $v_connection->exec("SET CHARACTER SET utf8");
            
            return $v_connection;
        } catch (Exception $ex) {
            Throw new Exception($ex);
        }
    }
    /**
     * 
     * @param \Oda\OdaPrepareReqSql $params
     * @return \Oda\OdaRetourReqSql
     * @throws SimpleObject\OdaException
     */
    public function reqODASQL(SimpleObject\OdaPrepareReqSql $params){
        $objRetour = new SimpleObject\OdaRetourReqSql();
        try {
            if($this->modeDebug){
                $params->debug = $this->modeDebug;
            }
            
            if($params->debug){
                echo'OdaPrepareReqSql : ';
                var_dump($params);
            }

            if($params->sql == ""){
                $objRetour->strErreur = self::ERROR_SQL_NOT_DEFINE;
                $objRetour->strStatut = self::SQL_STATUT_FAIL;
                goto gotoFinish;
            }

            //On rajoute le prefixe aux tables
            $sqlFinal = $this->addPrefixe($params->sql);
            
            //On prepare la requête
            $req = $this->connection->prepare($sqlFinal);

            foreach ($params->bindsValue as $key => $value){
                if(is_array($value)){
                    if(!isset($value["type"])){
                        if(is_int($value["value"])){
                            $param = PDO::PARAM_INT;
                        } elseif(is_bool($value["value"])) {
                            $param = PDO::PARAM_BOOL;
                        } elseif(is_null($value["value"])){
                            $param = PDO::PARAM_NULL;
                        } elseif(is_string($value["value"])){
                            $param = PDO::PARAM_STR;
                        } else {
                            $param = FALSE;
                        }
                        $req->bindValue(":".$key, $value["value"], $param);
                    }else{
                        $req->bindValue(":".$key, $value["value"], $value["type"]);
                    }
                }else{
                    if(is_int($value)){
                        $param = PDO::PARAM_INT;
                    } elseif(is_bool($value)) {
                        $param = PDO::PARAM_BOOL;
                    } elseif(is_null($value)){
                        $param = PDO::PARAM_NULL;
                    } elseif(is_string($value)){
                        $param = PDO::PARAM_STR;
                    } else {
                        $param = FALSE;
                    }
                    $req->bindValue(":".$key, $value, $param);
                }
            }

            switch ($params->typeSQL) {
                case self::SQL_GET_ONE:
                    //EXEMPLE SELECT 1 ROW
                    if($req->execute()){
                        $objRetour->nombre = $req->rowCount();
                        if($objRetour->nombre > 1){
                            $paramsEx = new stdClass();
                            $paramsEx->class = __CLASS__;
                            $paramsEx->function = __FUNCTION__;
                            $paramsEx->message = "Erreur SQL ODA, more one return for SQL_GET_ONE";
                            $paramsEx->code = self::ERROR_SQL_TOO_MUCH_RECORD;
                            $paramsEx->debug = $params->debug;
                            throw new SimpleObject\OdaException($paramsEx);
                        }else{
                            if(!empty($params->className)){
                                $req->setFetchMode(PDO::FETCH_CLASS, $params->className);
                                $objRetour->data = $req->fetch(PDO::FETCH_CLASS);
                            }else{
                                $objRetour->data = $req->fetch(PDO::FETCH_OBJ);
                            }
                        }
                    }else{
                        $paramsEx = new stdClass();
                        $paramsEx->class = __CLASS__;
                        $paramsEx->function = __FUNCTION__;
                        $pdoError = $req->errorInfo();
                        $paramsEx->message = "Erreur SQL => ".$pdoError[2];
                        $paramsEx->code = self::ERROR_SQL_SYNTAX;
                        $paramsEx->debug = $params->debug;
                        throw new SimpleObject\OdaException($paramsEx);
                    }
                    $req->closeCursor();
                    break;
                case self::SQL_GET_ALL:
                    //EXEMPLE SELECT N ROWS
                    $resultats = new stdClass();
                    if($req->execute()){
                        if(!empty($params->className)){
                            $resultats->data = $req->fetchAll(PDO::FETCH_CLASS, $params->className);
                        }else{
                            $resultats->data = $req->fetchAll(PDO::FETCH_OBJ);
                        }
                        $objRetour->nombre = count($resultats->data);
                        $resultats->nombre = $objRetour->nombre;
                        $objRetour->data = $resultats;
                    }else{
                        $paramsEx = new stdClass();
                        $paramsEx->class = __CLASS__;
                        $paramsEx->function = __FUNCTION__;
                        $pdoError = $req->errorInfo();
                        $paramsEx->message = "Erreur SQL => ".$pdoError[2];
                        $paramsEx->code = self::ERROR_SQL_SYNTAX;
                        $paramsEx->debug = $params->debug;
                        throw new SimpleObject\OdaException($paramsEx);
                    }
                    $req->closeCursor();
                    break;
                case self::SQL_INSERT_ONE:
                    $id = null;
                    if($req->execute()){
                        $id = $this->connection->lastInsertId(); 
                    }else{
                        $paramsEx = new stdClass();
                        $paramsEx->class = __CLASS__;
                        $paramsEx->function = __FUNCTION__;
                        $pdoError = $req->errorInfo();
                        $paramsEx->message = "Erreur SQL => ".$pdoError[2];
                        $paramsEx->code = self::ERROR_SQL_SYNTAX;
                        $paramsEx->debug = $params->debug;
                        throw new SimpleObject\OdaException($paramsEx);
                    }
                    $req->closeCursor();
                    $objRetour->nombre = 1;
                    $objRetour->data = $id;
                    break;
                case self::SQL_SCRIPT:
                    $resultReq = $req->execute();
                    if($resultReq){
                        $count = $req->rowCount();
                    }else{
                        $paramsEx = new stdClass();
                        $paramsEx->class = __CLASS__;
                        $paramsEx->function = __FUNCTION__;
                        $pdoError = $req->errorInfo();
                        $paramsEx->message = "Erreur SQL => ".$pdoError[2];
                        $paramsEx->code = self::ERROR_SQL_SYNTAX;
                        $paramsEx->debug = $params->debug;
                        throw new SimpleObject\OdaException($paramsEx);
                    }
                    $req->closeCursor();
                    $objRetour->nombre = $count;
                    $objRetour->data = $count;
                    break;
            }

            $objRetour->strStatut = self::SQL_STATUT_FINISH_OK;
            
            gotoFinish:

            if($params->debug){
                echo('OdaRetourReqSql : ');
                var_dump($objRetour);
            }
            
            return $objRetour;
        } catch (SimpleObject\OdaException $e) {
            $params = $e->getParams();
            if(($params->debug)||($this->modeDebug)){
                echo('SimpleObject\OdaException : ');
                var_dump($params);
            }
            $objRetour->statutCode = 4;
            $objRetour->strStatut = self::SQL_STATUT_FINISH_KO;
            $objRetour->erreurCode = -1;
            $objRetour->strErreur = $e->getMessage();
            $this->logTrace(0, "Function : " . __FUNCTION__ . ", error : " . $e->getMessage());
            return $objRetour;
        } catch (Exception $e) {
            $objRetour->statutCode = 4;
            $objRetour->strStatut = self::SQL_STATUT_FINISH_KO;
            $objRetour->erreurCode = -1;
            $objRetour->strErreur = $e->getMessage();
            $this->logTrace(0, "Function : " . __FUNCTION__ . ", error : " . $e->getMessage());
            return $objRetour;
        } 
    }
    /**
    * @name logTrace
    * @param int $p_niveau
    * @param string $p_msg
    */
    public function logTrace ($p_niveau, $p_msg) {
        try {
            $params = new stdClass();
            $params->sql = "INSERT INTO `".self::$config->prefixTable."api_tab_log`
               (`dateTime`,`type`,`commentaires`) 
               VALUES  
               (NOW(), :type, :msg)
            ;";
            $params->bindsValue = [
                "type" => [ "value" => $p_niveau],
                "msg" => [ "value" => addslashes($p_msg)]
            ];
            
            $req = $this->connection->prepare($params->sql);
            
            foreach ($params->bindsValue as $key => $value){
                if(!isset($value["type"])){
                    if(is_int($value["value"])){
                        $param = PDO::PARAM_INT;
                    } elseif(is_bool($value["value"])) {
                        $param = PDO::PARAM_BOOL;
                    } elseif(is_null($value["value"])){
                        $param = PDO::PARAM_NULL;
                    } elseif(is_string($value["value"])){
                        $param = PDO::PARAM_STR;
                    } else {
                        $param = FALSE;
                    }

                    $params->bindsValue[$key]["type"] = $param;
                    $req->bindValue(":".$key, $value["value"], $param);
                }else{
                    $req->bindValue(":".$key, $value["value"], $value["type"]);
                }
            }
            
            $resultReq = $req->execute();
            $req->closeCursor();
            
            $id = $this->connection->lastInsertId(); 

           return $id;
        } catch (Exception $ex) {
            Throw new Exception($ex);
        }
    }
    /**
    * @name getListTables
    * @param array
    */
    public function getListTables() {
        try {
            $params = new stdClass();
            $params->sql = "SHOW TABLES LIKE '".self::$config->prefixTable."%'
            ;";
            $req = $this->connection->prepare($params->sql);
            $req->execute();
            $resultats = $req->fetchAll();
            $req->closeCursor();
            
            $results = array();
            foreach ($resultats as $value){
                $strTables = str_replace(self::$config->prefixTable, "", $value[0]);
                $results[] = $strTables;
            }

            return $results;
        } catch (Exception $ex) {
            Throw new Exception($ex);
        }
    }
    /**
    * @name addPrefixe
    * @param string
    */
    public function addPrefixe($p_reqSql) {
        try {
            $strReturn = $p_reqSql;
            foreach ($this->listTables as $value){
                $strReturn = str_replace("`".$value."`", "`".self::$config->prefixTable.$value."`", $strReturn);
            }
            
            return $strReturn;
        } catch (Exception $ex) {
            Throw new Exception($ex);
        }
    }
    /**
    * @name getSingleObject
    * @p_param stdClass $p_params
    * $p_params->nameObj = "api_tab_parametres";
    * $p_params->keyObj = ["param_name" => "nom_site"];
    * $p_params->debug = false;
    * @param stdClass
    */
    public function getSingleObject ($p_params) {
        try {
            $params_attempt = new stdClass();
            $params_attempt->nameObj = null;
            $params_attempt->keyObj = null;
            $params_attempt->debug = false;

            $paramsInput = (object) array_merge((array) $params_attempt, (array) $p_params);

            $strSql = "SELECT * 
                FROM `".$paramsInput->nameObj."` a
                WHERE 1=1
            ";
            $bindsValue = [];

            foreach ($paramsInput->keyObj as $key => $value){
                $strSql .= "AND a.`".$key."` = :".$key."
                ";
                $bindsValue[$key] = [ "value" => $value];
            }

            $params = new SimpleObject\OdaPrepareReqSql();
            $params->sql = $strSql;
            $params->bindsValue = $bindsValue;
            $params->typeSQL = self::SQL_GET_ONE;
            $retour = $this->reqODASQL($params);

           return $retour->data;
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            $this->logTrace(0,$msg);
            Throw new Exception($ex);
       }
    }
    /**
     * @name setSingleObj
     * @p_param object $p_params
     * $p_params->nameObj = "api_tab_parametres";
     * $p_params->keyObj = ["param_name" => "nom_site"];
     * $p_params->setObj = ["param_value" => "nom_site2"];
     * @param stdclass
    */
    public function setSingleObj ($p_params) {
        try {
            $id = null;

            $params = new stdClass();
            $params->nameObj = $p_params->nameObj;
            $params->keyObj = $p_params->keyObj;
            $obj = $this->getSingleObject($params);

            if($obj){
                $strSql = "UPDATE `".$p_params->nameObj."` SET \n ";
                $bindsValue = [];

                //BIND
                foreach ($p_params->keyObj as $key => $value){
                    $bindsValue[$key] = [ "value" => $value];
                }

                foreach ($p_params->setObj as $key => $value){
                    $bindsValue[$key] = [ "value" => $value];
                }

                //SET
                foreach ($p_params->setObj as $key => $value){
                    $strSql .= " `".$key."` = :".$key." ,";
                }

                $strSql = substr($strSql, 0, -1);

                $strSql .= " \n WHERE 1=1  \n  ";

                //WHERE
                foreach ($p_params->keyObj as $key => $value){
                    $strSql .= " AND `".$key."` = :".$key.",";
                }

                $strSql = substr($strSql, 0, -1);
                
                $params = new SimpleObject\OdaPrepareReqSql();
                $params->sql = $strSql;
                $params->bindsValue = $bindsValue;
                $params->typeSQL = self::SQL_SCRIPT;
                $retour = $this->reqODASQL($params);
                $id = $obj->id;
            }else{
                $strSql = "INSERT INTO `".$p_params->nameObj."` \n (";
                $bindsValue = [];

                //BIND
                foreach ($p_params->keyObj as $key => $value){
                    $bindsValue[$key] = [ "value" => $value];
                }

                foreach ($p_params->setObj as $key => $value){
                    $bindsValue[$key] = [ "value" => $value];
                }

                //field
                foreach ($p_params->keyObj as $key => $value){
                    $strSql .= " `".$key."`,";
                }

                foreach ($p_params->setObj as $key => $value){
                    $strSql .= " `".$key."`,";
                }

                $strSql = substr($strSql, 0, -1);

                $strSql .= ") \n VALUES  \n ( ";

                //value
                foreach ($p_params->keyObj as $key => $value){
                    $strSql .= " :".$key.",";
                }

                foreach ($p_params->setObj as $key => $value){
                    $strSql .= " :".$key.",";
                }

                $strSql = substr($strSql, 0, -1);

                $strSql .= ")";

                $params = new SimpleObject\OdaPrepareReqSql();
                $params->sql = $strSql;
                $params->bindsValue = $bindsValue;
                $params->typeSQL = self::SQL_INSERT_ONE;
                $retour = $this->reqODASQL($params);
                $id = $retour->data;
            }

            return $id;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $this->logTrace(0,$msg);
            die($msg);
        }
    }
}