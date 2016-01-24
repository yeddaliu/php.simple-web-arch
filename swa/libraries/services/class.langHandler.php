<?php
class WebLangHandler{

    private $lang_module;	
    private $lang_locale;    
    private $lang_code;  
    
    /*
     * provide application owned language definition
     */
    public function WebLangHandler($langModule, $locale_code, $langRoot){    	
    	$this->lang_module = $langModule;
    	$this->lang_locale = $locale_code;

    	$this->lang_code = array();
        $global_lang_file = $langRoot.'/'.$this->lang_locale.'/lang.global.inc';
        if (file_exists($global_lang_file)===true) {
            //$gl_lang = parse_ini_file($global_lang_file, true, INI_SCANNER_RAW);
            $gl_lang = parse_ini_file($global_lang_file, true);
            if (!empty($gl_lang['global'])){
                $this->lang_code = array_merge($this->lang_code, $gl_lang['global']);
            }
        }
        
        $lang_file = $langRoot.'/'.$this->lang_locale.'/lang.'.$this->lang_module.'.inc';
        if (strcmp('global',$this->lang_module)!=0 && file_exists($lang_file)===true) {
            $lang = parse_ini_file($lang_file, true);
            if (!empty($lang[$this->lang_module])){
                $this->lang_code = array_merge($this->lang_code, $lang[$this->lang_module]); 
            }
        }
    }
			
    public function destroy() {
        unset($this->lang_module);	
        unset($this->lang_locale);
        //DO NOT unset $this->lang_code
        settype($this, 'null'); 
    }
        
    /* return boolean */
    public function isConfigLoaded() {
        return !empty($this->lang_code);
    }
	
    /* return boolean */
    public function isKeyExists($key) {
        
        return (self::isConfigLoaded() &&
                   !empty($key) &&
                   !empty($this->lang_code[$key])
                );
        
    }
        
    /* return string */
    /* pass arguments in term of index 0, 1, 2*/
    public function getMessage($key, $args=null) {

        if (self::isKeyExists($key)===false) {
            return '';
        }
    	
        $data = $this->lang_code[$key];

        if (!empty($args)) {
        	$search = array();        	
        	foreach ($args as $k=> $v) {
        		$search[$k] = '{'.$k.'}';
        	}
        	$data = str_replace($search, $args, $data);
        }

    	return $data;
    }    

    public function getLangLocale() {
        return $this->lang_locale;
    }

    public function getLangModuleName() {
        return $this->lang_module;
    }    
    
    public function getAllLangCode() {
        return $this->lang_code;
    }    
        
}

?>