<?php 
/*============================
 * Include files
 *    require_once()
 *    include_once(), etc...
 *===========================*/ 
require_once(UTILS_ROOT."/utils_data_filter.php");
require_once(DBMODEL_ROOT."/class.queue.php");
require_once(DBMODEL_ROOT."/class.smsservice.php");

/*============================
 * Public Variables
 *===========================*/
$view['testContact'] = array();
$view['testContact']['formData'] = array(); 

$sendAct = ($_POST && isset($_POST['sendAct']))? webDataFilter('p','sendAct','string'):'';
$name = $email = $mobileArea = $mobile = $sp = $msg = '';
/*============================
 * Public Functions
 *===========================*/
/*============================
 * Main execution
 *===========================*/
if (!empty($sendAct)) {
    $to_name = webDataFilter('p','name','string');
    $to_mail = webDataFilter('p','email','email');
    $mobileArea = webDataFilter('p','mobileArea','string');
    $mobile = webDataFilter('p','mobile','string');
    $sp = webDataFilter('p','sp','string');
    $msg = webDataFilter('p','msg','string');
    $to_sms = $mobileArea.$mobile.$sp ;
    $sub = $GLOBALS['MOD_LANG']->getMessage('contact.test.txt.subject');
    
    $paramSMS = array(
        "to_mail"=>$to_sms,
        "to_name"=>$to_name, 
        "mail_subject"=>$sub,
        "mail_body"=>$msg,
        //"from_mail"=> '',         
        //"from_name"=> ''
    );
    $paramEmail = array(
        "to_mail"=>$to_mail,
        "to_name"=>$to_name, 
        "mail_subject"=>$sub,
        "mail_body"=>$msg,
        //"from_mail"=> '',         
        //"from_name"=> ''
    );          

    $modelQueue = new Queue();
       
    switch ($sendAct) {
        case 'sms':
            $result = $modelQueue->setMailQueue($paramSMS);
            if ($result['success']===false) {
                $view['jsErrMsg'] = $GLOBALS['MOD_LANG']->getMessage('contact.test.send.sms.fail', array($result['errmsg']));                
            }
            else {
                $view['jsMsg'] = $GLOBALS['MOD_LANG']->getMessage('contact.test.send.sms.success', array($to_sms));                               
            }    
            //show view
        	break;
        case 'email':
        	$result = $modelQueue->setMailQueue($paramEmail);
            if ($result['success']===false) {
                $view['jsErrMsg'] = $GLOBALS['MOD_LANG']->getMessage('contact.test.send.email.fail', array($result['errmsg']));                
            }
            else {
                $view['jsMsg'] = $GLOBALS['MOD_LANG']->getMessage('contact.test.send.email.success', array($to_mail));                               
            }              
            //show view  
            break;            
        case 'all':         	                 
        	$resultSMS = $modelQueue->setMailQueue($paramSMS);
            $resultEmail = $modelQueue->setMailQueue($paramEmail);

            $errMsg = array();
            if ($resultSMS['success']===false) {
                $errMsg[] = $GLOBALS['MOD_LANG']->getMessage('contact.test.send.sms.fail', array($resultSMS['errmsg']));
            }
            if ($resultEmail['success']===false) {
                $errMsg[] = $GLOBALS['MOD_LANG']->getMessage('contact.test.send.email.fail', array($resultEmail['errmsg']));                    
            }

            if (count($errMsg)>0) {
            	$view['jsErrMsg'] = implode('<br>',$errMsg);
            }
            else {
            	$view['jsMsg'] = $GLOBALS['MOD_LANG']->getMessage('contact.test.send.all.success', array($to_sms,$to_mail));            	
            }            
            break;
        default:
            header("location: ".WEB_ROOT.'/testContact/');
            exit();
            break;
    }   
    
}

$modelSP = new SMSService();
//$sp will be reset if post data is exists
$view['testContact']['spOption'] = $modelSP->genServiceOptionsbyDomain($sp);

$view['testContact']['formData'] = array(
    'name'=>&$to_name,
    'email'=>&$to_mail,
    'mobile_area'=>&$mobileArea,
    'mobile'=>&$mobile,
    'msg'=>&$msg
);
/*============================
 * View Loading
 *===========================*/
include("view/b_sendContactMsg.php");
?>   