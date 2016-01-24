<?php
if (defined('DEFINE_GLOBAL_FLAG')) {
    return ;
}
define ('DEFINE_GLOBAL_FLAG', 1) ;

/* debug */
define('APP_DEBUG_MODE',1);

/*CHANGABLE FILE PATH::Always use absolutley path*/
define('CORE_MODULES_ROOT',dirname(__FILE__).'/../../../CoreModules');

define('LIBRARY_ROOT',dirname(__FILE__).'/../');
define('TRACE_LOG_ROOT',dirname(__FILE__).'/../../logs');
define('MEDIA_ROOT',dirname(__FILE__).'/../../media_space');

/*internal project setting*/
define('GLOBAL_DEF_ROOT',LIBRARY_ROOT.'/global_define');
define('GLOBAL_LANG_ROOT',GLOBAL_DEF_ROOT.'/locale');
define('SERVICE_ROOT',LIBRARY_ROOT.'/services');
define('DBMODEL_ROOT',LIBRARY_ROOT.'/dbmodel');
define('EX_PKGS_ROOT',LIBRARY_ROOT.'/pkgs');
define('UTILS_ROOT',LIBRARY_ROOT.'/common_utils');


/*session*/
define('TIME_OUT','7200');

/* multiuser logins */
define ("MULTILOGIN_MAX_CUST", 50) ;
define ("MULTILOGIN_MAX_DEALER", 20) ;


/*other definitions*/

?>
