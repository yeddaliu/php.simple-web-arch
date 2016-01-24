<?php 
/*============================
 * Include files
 *    require_once()
 *    include_once(), etc...
 *===========================*/ 
require_once(DBMODEL_ROOT."/class.security.php");

/*============================
 * Public Variables
 *===========================*/
$modelSecurity = null;
/*============================
 * Public Functions
 *===========================*/
/*============================
 * Main execution
 *===========================*/
$modelSecurity = new Security();
$modelSecurity->recordLogout(WebSession::get(PRODUCT_ID,'tno'), session_id(), getenv('REMOTE_ADDR'));

WebSession::destroy(PRODUCT_ID);
    
header('location: '.WEB_ROOT.'/login/');
exit();

/*============================
 * View Loading
 *===========================*/
?>
