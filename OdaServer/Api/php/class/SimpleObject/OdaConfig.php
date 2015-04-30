<?php
namespace Oda\SimpleObject;
/**
 * LIBODA Librairy - main class
 *
 * Tool
 *
 * @author  Fabrice Rosito <rosito.fabrice@gmail.com>
 * @version 0.150128
 */

class OdaConfig {    
    /**
     *
     * @var OdaConfig 
     */
    private static $instance;
    /**
     * ex : http://localhost/WORK/
     * Mandatory
     * @var string 
     */
    public $domaine;
    /**
     * @var string 
     */
    public $resourcesPath;
    /**
     * @var string 
     */
    public $resourcesLink;
    /**
     * Mandatory
     * @var OdaConnection 
     */
    public $BD_ENGINE;
    /**
     * Optional
     * @var OdaConnection 
     */
    public $BD_AUTH;
    /**
     * @var OdaMailgunConf 
     */
    public $MAILGUN;
    
    /**
     * class constructor
     *
     * @param stdClass $p_params
     * @return OdaDate $this
     */
    public function __construct(){
        $this->BD_ENGINE = new OdaConnection();
        $this->BD_AUTH = new OdaConnection();
        $this->MAILGUN = new OdaMailgunConf();
    }
    /**
     * Destructor
     *
     * @access public
     * @return null
     */
    public function __destruct(){
        
    }  
    /**
     * 
     * @return OdaConfig
     */
    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new OdaConfig();
            self::$instance->BD_ENGINE = new OdaConnection();
            self::$instance->BD_AUTH = new OdaConnection();
            self::$instance->MAILGUN = new OdaMailgunConf();
        }
        return self::$instance; 
    }
    /**
     * isOK
     *
     * @access public
     * @return boolean
     */
    public function isOK(){
        if(is_null($this->domaine)||(empty($this->domaine))){
            $params = new \stdClass();
            $params->class = __CLASS__;
            $params->function = __FUNCTION__;
            $params->message = "Configuration of domaine is missing.(config.php)";
            throw new OdaException($params);
        }
        return true;
    }
}
class OdaConnection {    
    /**
     * ex : http://localhost/WORK/
     * Mandatory
     * @var string 
     */
    public $host = 'localhost';
    /**
     * ex : localhost
     * Mandatory
     * @var string 
     */
    public $base;
    /**
     * Optional
     * @var string 
     */
    public $login;
    /**
     * Mandatory
     * @var string 
     */
    public $mdp;
    /**
     * Optional
     * @var string 
     */
    public $prefixTable = '';
    /**
     * Optional
     * @var string 
     */
    public $type = 'mysql';
    /**
     * Optional
     * @var string 
     */
    public $port = '3306';
    
    /**
     * class constructor
     *
     * @param stdClass $p_params
     * @return OdaDate $this
     */
    public function __construct(){
    }
    /**
     * Destructor
     *
     * @access public
     * @return null
     */
    public function __destruct(){
        
    }    
    /**
     * isOK
     *
     * @access public
     * @return boolean
     */
    public function isOK(){
        if(
            (is_null($this->base)||(empty($this->base)))
            ||
            (is_null($this->mdp)||(empty($this->mdp)))
        ){
            $params = new \stdClass();
            $params->class = __CLASS__;
            $params->function = __FUNCTION__;
            $params->message = "Configuration of base or mdp is missing.(config.php)";
            throw new OdaException($params);
        }
        return true;
    }
}
class OdaMailgunConf {    
    /**
     * Mandatory
     * @var string 
     */
    public $api_key;
    /**
     * Mandatory
     * @var string 
     */
    public $domaine;
    
    /**
     * class constructor
     *
     * @param stdClass $p_params
     * @return OdaDate $this
     */
    public function __construct(){
    }
    /**
     * Destructor
     *
     * @access public
     * @return null
     */
    public function __destruct(){
        
    }    
    /**
     * isOK
     *
     * @access public
     * @return boolean
     */
    public function isOK(){
        $boolReturn = false;
        
        if( ((!is_null($this->api_key))&&($this->api_key!="")) && ((!is_null($this->domaine))&&($this->domaine!="")) ){
            $boolReturn = true;
        }
        
        return $boolReturn;
    }
}