<?php
namespace Oda;
/**
 * 
 */
class AutoLoader{
    /**
     * 
     */
    static function register(){
        spl_autoload_register(array(__CLASS__,'autoload'));
    }
    
    /**
     * 
     * @param type $class
     */
    static function autoload($class){
        $sep = self::getSperateur();
        if(strpos($class, __NAMESPACE__ . "\\") === 0){
            $class = str_replace(__NAMESPACE__ . "\\", '', $class);
            $file_base = dirname(__FILE__).$sep.$class.'.php';
            require_once $file_base;
        }
    }
    
    /**
     * 
     * @return string
     */
    static function getSperateur(){
        $file_base_win = dirname(__FILE__).'\\AutoLoader.php';
        if(file_exists($file_base_win)){
            return '\\';
        }else{
            return '/';
        }
    }
    
}