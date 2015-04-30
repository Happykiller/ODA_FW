<?php
namespace Oda\SimpleObject;
class OdaException extends \Exception { 
    protected $_params;
    protected $_class;

    public function __construct($p_params) {
        $params_attempt = new \stdClass();
        $params_attempt->class = NULL;
        $params_attempt->function = NULL;
        $params_attempt->previousException = NULL;
        $params_attempt->message = NULL;
        $params_attempt->code = NULL;
        $params_attempt->params = NULL;
        
        $params = (object) array_merge((array) $params_attempt, (array) $p_params);

        $this->_params = $params;
        $this->_class = __CLASS__;

        // assurez-vous que tout a été assigné proprement
        parent::__construct($params->message, $params->code, $params->previousException);
    }

    public function __toString() {
        $strEcho = $this->_class." =>";
        
        if(!is_null($this->_params->class)){
            $strEcho .= " From class : ".$this->_params->class.",";
        }
        
        if(!is_null($this->_params->class)){
            $strEcho .= " From function : ".$this->_params->function.",";
        }
        
        if(!is_null($this->_params->previousException)){
            $strEcho .= " \Exception Message : ".$this->_params->previousException->message.",";
        }
        
        if(!is_null($this->_params->code)){
            $strEcho .= " Code : ".$this->_params->code.",";
        }
        
        if(!is_null($this->_params->message)){
            $strEcho .= " Message : ".$this->_params->message.",";
        }
        
        if(!is_null($this->_params->params)){
            $strEcho .= " Params : ".get_class($this->_params->params);
        }
        
        return $strEcho;
    }

    public function getParams() {
        return $this->_params;
    }

    public function getClass() {
        return $this->_class;
    }
}