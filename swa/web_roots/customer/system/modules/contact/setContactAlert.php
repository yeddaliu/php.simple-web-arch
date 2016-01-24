<?php 
/*============================
 * Include files
 *    require_once()
 *    include_once(), etc...
 *===========================*/ 
require_once(DBMODEL_ROOT."/class.contact.php");

/*============================
 * Public Variables
 *===========================*/
$view = array();
        
$ctno = 0;
if (isset($_GET['ctno']) && !empty($_GET['ctno'])) {
    $ctno = $_GET['ctno'];
} 

$action = '';
/*============================
 * Public Functions
 *===========================*/
function genChecedkArray($target) {
    //i=3~4 will never use in this product
	$data = array();
	for ($i=0; $i<=6; $i++) {
        if (in_array($i, $target)===true) {
            $data[$i] = 'CHECKED';     
        }
        else { $data[$i] = ''; }
    }
    return $data;	
}
/*============================
 * Main execution
 *===========================*/
$modelContact = new AlertContact();

if ($_POST) {	
	$result = $modelContact->updateContactAlert($_POST['ctno'], $_POST['emailTypes'], $_POST['mobileTypes']);   
	if ($result['success']===false) {
        $view['jsErrMsg'] = &$result['errmsg'];
        //show add UI using post data        
        $ctno =  &$_POST['ctno'];
        $emailType = explode(',',$_POST['emailTypes']);
        $view['emailCheck'] = genChecedkArray($emailType);
        $mobileType = explode(',',$_POST['mobileTypes']);
        $view['mobileCheck'] = genChecedkArray($mobileType);
                                            
        $action = 'modifyPost';
        //no exit                
    }
    else {
        //header("location: ".WEB_ROOT.'/contact/');
	    printHTMLContent('genStandalongJSBlock', array(
	       genJSExistBoxWithCloseRedirect(WEB_JS_OKMSGBOX_ID, $GLOBALS['MOD_LANG']->getMessage('gl.txt.update.success'), (WEB_ROOT.'/contact/'))
	    ));     
        exit();                 
    }        	
}


/*============================
 * View Loading
 *===========================*/
if (empty($action)) {
	$result = $modelContact->getContact($ctno);
    if ($result['success']===false) {
        printHTMLContent('genErrorModuleHTML', array(
            $GLOBALS['MOD_LANG']->getMessage('contact.alert.legend'), $GLOBALS['MOD_LANG']->getMessage('gl.zone.err.title'),$result['errmsg']
        ));  
        exit();   
    }
    
    $emailType = explode(',',$result['data']['email_alert']);
    $view['emailCheck'] = genChecedkArray($emailType);
    $mobileType = explode(',',$result['data']['sms_alert']);
    $view['mobileCheck'] = genChecedkArray($mobileType);
} 
$view['ctno'] = &$ctno;

include("view/b_modifyContactAlert.php");
?>   