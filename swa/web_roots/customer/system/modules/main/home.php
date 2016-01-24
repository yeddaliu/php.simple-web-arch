<?php 
/*============================
 * Include files
 *    require_once()
 *    include_once(), etc...
 *===========================*/ 

/*============================
 * Public Variables
 *===========================*/
$view = array();

/*============================
 * Public Functions
 *===========================*/
/*============================
 * Main execution
 *===========================*/
/*============================
 * View Loading
 *===========================*/
$view['title'] = $GLOBALS['MOD_LANG']->getMessage('html.title', array(PRODUCT_NAME));
$view['footer'] = $GLOBALS['MOD_LANG']->getMessage('html.right', array(PRODUCT_RIGHT, PRODUCT_VERSION));

$view['header'] = array (
    "logo"=>WEB_IMG_ROOT.'/logo/logo_pms-250x56.gif',
    "brand"=>$GLOBALS['MOD_LANG']->getMessage('home.title')  
);
include("view/v_home.php");
?>
