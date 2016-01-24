<?php
include_once(PRODUCT_DEF_ROOT.'/module_registry.inc');  
    
class ModuleHandler{
	
    /* return boolean */
    public static function isConfigLoaded() {
        return isset($GLOBALS['MODULE_REGISTRY']);
    }
    
    /* return boolean */
    public static function isModuleExists($mid) {

        return (self::isConfigLoaded() &&
                   !empty($mid) &&
                   !empty($GLOBALS['MODULE_REGISTRY'][$mid])
                );
        
    }

    /* return String*/
    public static function getModulePath($mid, $dirRoot='') {
        
        if (self::isModuleExists($mid)===false) {
            return '';
        }

        $mod = $GLOBALS['MODULE_REGISTRY'][$mid];                  
        return "$dirRoot/{$mod['module']}/{$mod['control']}";           
    }
    
    /* return boolean */
    public static function isLangModuleExists($mid) {

        return (self::isModuleExists($mid) &&
                   !empty($GLOBALS['MODULE_REGISTRY'][$mid]['lang_module'])
                );
        
    }
    
    /* return String */
    public static function getLangModuleName($mid) { 
        if (self::isLangModuleExists($mid)===false) {  
            return '';
        }
        return $GLOBALS['MODULE_REGISTRY'][$mid]['lang_module'];           
    }
    
}

?>