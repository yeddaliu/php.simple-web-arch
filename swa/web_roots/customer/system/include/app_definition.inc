<?php 
if (defined('DEFINE_APP_CUSTOMER_STATIC'))  return;
define ('DEFINE_APP_CUSTOMER_STATIC', 1) ;

define('APP_DEBUG_MODE','1');

/*FILE INFO*/
define('PRJ_LIB_ROOT','/swa/libraries');

define('PRODUCT_ROOT',dirname(__FILE__).'/../..');    
define('PRODUCT_SYS_ROOT',PRODUCT_ROOT.'/system');
define('PRODUCT_DEF_ROOT',PRODUCT_SYS_ROOT.'/include');
define('PRODUCT_LIB_ROOT',PRODUCT_SYS_ROOT.'/libs');
define('PRODUCT_MODULES_ROOT',PRODUCT_SYS_ROOT.'/modules'); 
define('PRODUCT_LANG_ROOT',PRODUCT_DEF_ROOT.'/locale');
define('PRODUCT_IMG_ROOT',PRODUCT_ROOT.'/images');
//define('PRODUCT_CSS_ROOT',PRODUCT_ROOT.'/styles');
define('PRODUCT_JS_ROOT',PRODUCT_ROOT.'/scripts');        
    
/*PRODUC INFO*/
define('PRODUCT_NAME','AccuBright');
define('PRODUCT_VERSION','v0.1');
define('PRODUCT_RIGHT','&copy; '.date("Y").' Powered by Commtiva Technology');    //© 2010 Powered by Commtiva Technology
define('PRODUCT_ID','CUST'); //SLDEALER  
    
/*WEB INFO*/
define('WEB_ROOT','/customer');    //set '' if locate in domain root 
define('WEB_CONF_ROOT',WEB_ROOT.'/include');
define('WEB_MODULES_ROOT',WEB_ROOT.'/modules');
define('WEB_IMG_ROOT',WEB_ROOT.'/images');
define('WEB_CSS_ROOT',WEB_ROOT.'/styles');
define('WEB_JS_ROOT',WEB_ROOT.'/scripts');  
    
define('WEB_APP_TYPE',2);   //0: admin, 1: dealer, 2: customer 3: account manager
define('WEB_APP_MANAGER_TYPE',3);
define('WEB_SESSION_NAME', PRODUCT_ID.'-session');   
    
/*WEB SETTING*/
define('WEB_LANG','en_US');
define('WEB_THEMES','dark-hive');
define('WEB_PAGE_RECORDS',10);

?>