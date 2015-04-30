<?php
namespace Oda\SimpleObject;
/* 
 * Class OdaDate
 * ex : 
 * $params = new stdClass();
 * $params->format = 'j-M-Y';
 * $params->strDate = '15-Feb-2009';
 * $odaDate = new OdaDate($params)
 */
class OdaDate extends \DateTime{
    private $_params;
    protected $_year;
    protected $_month;
    protected $_day;
    protected $_hour;
    protected $_minute;
    protected $_seconde;
    protected $_microseconds;
    const FORMAT_DATE_FR = 'd/m/Y';

    /**
     * class constructor
     *
     * @param stdClass $p_params
     * @return OdaDate $this
     */
    public function __construct($p_params = NULL){
        $params_attempt = new \stdClass();
        $params_attempt->strDate = NULL;
        $params_attempt->format = NULL;
        $params_attempt->debug = false;
        try {
            $params = (object) array_merge((array) $params_attempt, (array) $p_params);

            $this->_params = $params;

            if(is_null($params->strDate)){
                parent::__construct();
            }else{
                if(is_null($params->format)){
                    parent::__construct($params->strDate);
                }else{
                    $date = \DateTime::createFromFormat($params->format, $params->strDate);
                    parent::__construct($date->format($params->format));
                }
            }
            
            $this->_year = $this->getNumYear();
            $this->_month = $this->getNumMonth();
            $this->_day = $this->getNumDay();
            $this->_hour = $this->getHour();
            $this->_minute = $this->getMinute();
            $this->_seconde = $this->getSeconde();
            $this->_microseconds = $this->getMicroseconds();

            return $this;
        } catch (OdaException $e){
            die($e);
        }  catch (\Exception $e){
            die($e);
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
    /**
     * getParams
     *
     * @access public
     * @return sdClass
     */
    public function getParams() {
        try {
            return $this->_params;
        }  catch (\Exception $e){
            die($e);
        }    
    }
    /**
     * getNumYear
     *
     * @access public
     * @return int
     */
    public function getNumYear() {
        try {
            return intval($this->format('Y'));
        }  catch (\Exception $e){
            die($e);
        }    
    }
    /**
     * getNumMonth
     *
     * @access public
     * @return int
     */
    public function getNumMonth() {
        try {
            return intval($this->format('n'));
        }  catch (\Exception $e){
            die($e);
        }    
    }
    /**
     * getNumDay
     *
     * @access public
     * @return int
     */
    public function getNumDay() {
        try {
            return intval($this->format('d'));
        }  catch (\Exception $e){
            die($e);
        }    
    }
    /**
     * getHour
     *
     * @access public
     * @return int
     */
    public function getHour() {
        try {
            return intval($this->format('G'));
        }  catch (\Exception $e){
            die($e);
        }    
    }
    /**
     * getMinute
     *
     * @access public
     * @return int
     */
    public function getMinute() {
        try {
            return intval($this->format('i'));
        }  catch (\Exception $e){
            die($e);
        }    
    }
    /**
     * getSeconde
     *
     * @access public
     * @return int
     */
    public function getSeconde() {
        try {
            return intval($this->format('s'));
        }  catch (\Exception $e){
            die($e);
        }    
    }
    /**
     * getMicroseconds
     *
     * @access public
     * @return int
     */
    public function getMicroseconds() {
        try {
            $t = microtime(true);
            $micro = sprintf("%06d",($t - floor($t)) * 1000000);
            return intval($micro);
        }  catch (\Exception $e){
            die($e);
        }    
    }
    /**
     * getDateTimeWithMili
     *
     * @access public
     * @return string|\DateTime
     */
    public function getDateTimeWithMili() {
        try {
            $t = microtime(true);
            $micro = sprintf("%03d",($t - floor($t)) * 1000);
            return $this->format("Y-m-d H:i:s.").$micro;
        }  catch (\Exception $e){
            die($e);
        }    
    }
    /**
    * class getMilis
    *
    * @access public
    * @return int
    */
    static function getMicro(){
        $retour = 0;
        try {
            $retour = microtime(true);
            return $retour;
        } catch (\Exception $e) {
           $msg = $e->getMessage();
           Throw new \Exception('Erreur dans '.__CLASS__.' : '.$msg);
           return null;
        }
    }
}
