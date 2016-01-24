<?php
/*
|---------------------------------------------------------------
| PHP ERROR REPORTING LEVEL
|---------------------------------------------------------------
|
| By default CI runs with error reporting set to ALL.  For security
| reasons you are encouraged to change this when your site goes live.
| For more info visit:  http://www.php.net/error_reporting
|
*/
error_reporting(E_ALL & ~E_NOTICE);

/*
 * app definitions
 */
require_once('system/include/app_init.inc');

/*
 * app common library, loaded after security check
 */
require_once(SERVICE_ROOT.'/class.langHandler.php');
require_once(PRODUCT_LIB_ROOT.'/common.php');
require_once(PRODUCT_LIB_ROOT.'/class.moduleHandler.php');

/* 
 * parse request & check module 
 */
$MOD_ID = empty($MOD_ID)?'home':$MOD_ID;
if (!ModuleHandler::isModuleExists($MOD_ID)) {
    //redirect to home page if login already, or redirect to login page
    header('location: '.WEB_ROOT);       
    exit();
}  
if (strcmp('login', $MOD_ID) != 0) {
        WebSession::put(PRODUCT_ID, 'last_mod_id', $MOD_ID);    
}        

/*
 * prepare page level variables
 * 
 * please use $GLOBALS['MOD_ID'], $GLOBALS['MOD_LANG']...
 * to get the variables whitin module programs. 
 */
$MOD_LANG = new WebLangHandler(ModuleHandler::getLangModuleName($MOD_ID), 
                                WEB_LANG, PRODUCT_LANG_ROOT);               
/* 
 * call module controller
 */
require_once(ModuleHandler::getModulePath($MOD_ID, PRODUCT_MODULES_ROOT));

?>