<?php 
/*============================
 * Include files
 *    require_once()
 *    include_once(), etc...
 *===========================*/ 
require_once(UTILS_ROOT."/utils_data_filter.php");
require_once(DBMODEL_ROOT."/class.contact.php");
require_once(DBMODEL_ROOT."/class.smsservice.php");

/*============================
 * Public Variables
 *===========================*/
$view['setContact'] = array();
$view['setContact']['formData'] = array();           

$action = webDataFilter('g','act','string');
if (empty($action)) { $action = 'add'; }

$ctno = webDataFilter('g','ctno','int');
if (is_null($ctno)) { $ctno = 0; }

$sendAct = ($_POST && isset($_POST['sendAct']))? webDataFilter('p','sendAct','string'):'';    
$name = $email = $mobileArea = $mobile = $sp = '';

/*============================
 * Public Functions
 *===========================*/
function printSuccessMsgBox() {    
    printHTMLContent('genStandalongJSBlock', array(
        genJSExistBoxWithCloseRedirect(WEB_JS_OKMSGBOX_ID, $GLOBALS['MOD_LANG']->getMessage('gl.txt.update.success'), (WEB_ROOT.'/contact/'))    
    ));            	
}

/*============================
 * Main execution
 *===========================*/
$modelContact = new AlertContact();

if (!empty($sendAct)) {
	
    $name = webDataFilter('p','name','string');
    $email = webDataFilter('p','email','email');
    $mobileArea = webDataFilter('p','mobileArea','string');
    $mobile = webDataFilter('p','mobile','string');
    $sp = webDataFilter('p','sp','string');

	switch ($sendAct) {
		case 'add':
            $param = array(
               "login_no"=> WebSession::get(PRODUCT_ID, 'tno'),
               "name"=> &$name,
               "email"=> &$email,
               "mobile_area"=> &$mobileArea,
               "mobile"=> &$mobile,
               "sp"=>  &$sp
            );

			$result = $modelContact->addContact($param);
            if ($result['success']===false) {
            	$view['jsErrMsg'] = &$result['errmsg'];            	
                //show add UI using post data
                $action = 'addPost';
                //no exit                
            }
            else {
            	//header("location: ".WEB_ROOT.'/contact/');
            	printSuccessMsgBox();
                exit();                 
            }        
            break;            
		case 'modify':
            $param = array(                
               "name"=> &$name,
               "email"=> &$email,
               "mobile_area"=> &$mobileArea,
               "mobile"=> &$mobile,
               "sp"=>  &$sp
            );

            $result = $modelContact->updateContact(webDataFilter('p','ctno','int'), $param);
            if ($result['success']===false) {
                $view['jsErrMsg'] = &$result['errmsg'];                
                //show modify UI using post data
                $action = 'modifyPost';                                 
                //no exit                
            }
            else {
                //back to contact list                
                //header("location: ".WEB_ROOT.'/contact/');
            	printSuccessMsgBox();
                exit(); 	            
            }        			
			break;
        case 'del':                	        	
        	$result = $modelContact->delContact(webDataFilter('p','ctno','int'));
        	if ($result['success']===false) {   
                printHTMLContent('genStandalongJSBlock', array(
                    genJSExistBoxWithCloseRedirect(WEB_JS_ERRMSGBOX_ID, $result['errmsg'], (WEB_ROOT.'/contact/'))
                ));
                exit();
            }            
            else {
            	printSuccessMsgBox();
            	exit();
            }                                   
            break;
	    default:
	    	header("location: ".WEB_ROOT.'/contact/');
	    	exit();
			break;
	}	
}

$modelSP = new SMSService();

/*============================
 * View Loading
 *===========================*/
switch ($action) {
	case 'add':
        $view['setContact']['spOption'] = $modelSP->genServiceOptionsbyIdx();
        include("view/b_addContact.php");       
		break;
    case 'addPost': 
        $view['setContact']['formData'] = array (
            'contact_name' => &$name,
            'contact_email' => &$email,
            'contact_mobile_area' => &$mobileArea,
            'contact_mobile' => &$mobile,
            'sms_service' => &$sp
        );
    	$view['setContact']['spOption'] = $modelSP->genServiceOptionsbyIdx($view['setContact']['formData']['sms_service']);
        include("view/b_addContact.php");    	
        break;    
    case 'modify':
        $result = $modelContact->getContact($ctno);
        if ($result['success']===false) {
            printHTMLContent('genErrorModuleHTML', array(
                $GLOBALS['MOD_LANG']->getMessage('contact.modify.legend'), $GLOBALS['MOD_LANG']->getMessage('gl.zone.err.title'), $result['errmsg']
            ));             
            exit();
        }
        $view['setContact']['formData'] = &$result['data'];    
        $view['setContact']['ctno'] = &$ctno;
        $view['setContact']['spOption'] = $modelSP->genServiceOptionsbyIdx($view['setContact']['formData']['sms_service']);
        include("view/b_modifyContact.php");            
        break;      
    case 'modifyPost':   
        $view['setContact']['formData'] = array (
            'contact_name' => &$name,
            'contact_email' => &$email,
            'contact_mobile_area' => &$mobileArea,
            'contact_mobile' => &$mobile,
            'sms_service' => &$sp
        );
        $view['setContact']['ctno'] = webDataFilter('p','ctno','int'); 
        $view['setContact']['spOption'] = $modelSP->genServiceOptionsbyIdx($view['setContact']['formData']['sms_service']);
        include("view/b_modifyContact.php");        	
        break;      
}
unset($view['setContact']);
?>   