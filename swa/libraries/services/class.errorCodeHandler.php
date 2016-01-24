<?php
if (!defined('WEB_LANG')) {
	define('WEB_LANG', 'en_US');
}
if (!defined('GLOBAL_LANG_ROOT')) {
    define('GLOBAL_LANG_ROOT', dirname(__FILE__).'/../global_define/locale');
}

class ErrorCodeHandler{

    private static $instance;
    
    private $lang_locale; 
    private $err_code;
    
    /*
     * provide application owned language definition
     */    
    public static function getInstance() {
        if (!isset(self::$instance)) {
            $classname = __CLASS__;
            self::$instance = new $classname;
        }

        return self::$instance;
    }    
        
    private function __construct() {
        
        $this->lang_locale = WEB_LANG;

        $this->err_code = array();
        $lang_file = GLOBAL_LANG_ROOT.'/'.$this->lang_locale.'/errorcode.inc';
        if (file_exists($lang_file)===true) {
            $err = parse_ini_file($lang_file, true);
            if (!empty($err['err_code'])){
                $this->err_code = $err['err_code'];
            }
        }
        
    }

    public function __clone() {
        trigger_error('Clone ErrorCodeHandler is not allowed.', E_USER_ERROR);
    }
    	
    /* return boolean */
    public function isConfigLoaded() {
        return !empty($this->err_code);
    }
    
    /* return boolean */
    public function isCodeExists($code) {
        
        return (self::isConfigLoaded() &&
                   !empty($code) &&
                   !empty($this->err_code[$code])
                );
        
    }
        
    /* return string */
    /* pass arguments in term of index 0, 1, 2*/
    public function getErrorMessage($code='0001', $args=null) {

        if (self::isCodeExists($code)===false) {
            return '';
        }
        
        $data = $this->err_code[$code];

        if (!empty($args)) {
            $search = array();          
            foreach ($args as $k=> $v) {
                $search[$k] = '{'.$k.'}';
            }
            $data = str_replace($search, $args, $data);
        }

        return $data;
    }    
    
    public function getErrorLocale() {
        return $this->lang_locale;
    }
    
    public function getAllErrorCode() {
        return $this->err_code;
    }       
}

?>