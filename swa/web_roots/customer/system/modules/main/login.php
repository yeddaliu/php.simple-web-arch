<?php 
/*============================
 * Include files
 *    require_once()
 *    include_once(), etc...
 *===========================*/ 
require_once(UTILS_ROOT."/utils_data_filter.php");
require_once(DBMODEL_ROOT."/class.security.php");

/*============================
 * Public Variables
 *===========================*/
$view = array();
$modelSecurity = null;
$jsErrMsg = '';
/*============================
 * Public Functions
 *===========================*/
function errorAlert($error) {
    if (strlen($error)>0) {
        echo '<script>alert("'.$error.'");location.href="'.WEB_ROOT.'/login/";</script>';
        exit();
    }	
}
/*============================
 * Main execution
 *===========================*/
if ($_POST) {

	$uid = webDataFilter('p','userid','string');
	$password = md5(webDataFilter('p','password','string'));

	$modelSecurity = new Security();
	
	/* customer level accepts logins: customer & account manager */
	$result = $modelSecurity->checkLogin($uid, $password, WEB_APP_TYPE);
    if ($result['success']===false) {    	
        if ($result['errcode']=='1001') {
            $resultManager = $modelSecurity->checkLogin($uid, $password, WEB_APP_MANAGER_TYPE);    	
            if ($resultManager['success']===false) {
                $jsErrMsg = $resultManager['errmsg'];            	
            }    	        	
            else {
            	$userInfo = $resultManager['data'];
            }
        }    	
    	else {
	        $jsErrMsg = $result['errmsg'];    		
    	}
    }   
    else {
    	$userInfo = $result['data'];
    }
    
    errorAlert($jsErrMsg);
    
    /*
     * cache user info
     * register session info  and redirect to home page
     */
    WebSession::destroy(PRODUCT_ID);        
    
    $resultLogin = $modelSecurity->recordLogin($userInfo['login_no'], session_id(), getenv('REMOTE_ADDR'));
    if ($resultLogin['success']===false) {
        errorAlert($resultLogin['errmsg']);
    }
    else {
        WebSession::put(PRODUCT_ID, 'pass', 1);
    
        WebSession::put(PRODUCT_ID, 'tid', $userInfo['login_id']);
        WebSession::put(PRODUCT_ID, 'tno', $userInfo['login_no']);
        WebSession::put(PRODUCT_ID, 'tna', $userInfo['first_name'].' '.$userInfo['last_name']);     
        WebSession::put(PRODUCT_ID, 'op', $userInfo['acc_type']);
        WebSession::put(PRODUCT_ID, 'sid', session_id());
        WebSession::put(PRODUCT_ID, 'creater', $userInfo['creater']);
        //WebSession::put(PRODUCT_ID, 'level', 'customer');
        WebSession::put(PRODUCT_ID, 'subacc', $userInfo['sub_acc']);        
        WebSession::put(PRODUCT_ID, 'gno', $userInfo['group_no']);
        WebSession::put(PRODUCT_ID, 'mdno', $userInfo['master_dealer']);
            
        //redirect to home page
        header('location: '.WEB_ROOT.'/home/');
        exit();         
    }    
}

/*============================
 * View Loading
 *===========================*/
$view['title'] = $GLOBALS['MOD_LANG']->getMessage('html.title', array(PRODUCT_NAME));
$view['footer'] = $GLOBALS['MOD_LANG']->getMessage('html.right', array(PRODUCT_RIGHT, PRODUCT_VERSION));

include("view/v_login.php");
?>
