<?php
require_once(SERVICE_ROOT.'/class.session.php');
require_once(DBMODEL_ROOT.'/class.security.php');

WebSession::beforeStart(PRODUCT_ID);
session_start();

$cache_tno = WebSession::get(PRODUCT_ID, 'tno');
if (!empty($cache_tno)) {    
    if(empty($MOD_ID) || !in_array($MOD_ID, $ESCAPE_MOD_ID)) {
        //get login session count
        $modelSecurity = new Security();
        $result_session = $modelSecurity->isSessionExists($cache_tno, session_id(), getenv('REMOTE_ADDR'));
        if ($result_session['success']===false) {
            header('Location: '.WEB_ROOT.'/logout/');
            exit(); 	       	
        }
        $isSessionExists = $result_session['data'];

        //check login pass
        if ($isSessionExists===false || WebSession::get(PRODUCT_ID, 'pass')!=1 ) {
            header('Location: '.WEB_ROOT.'/logout/');
            exit();
        }
	            
        //auto logout ------Start (2 hour)
        WebSession::checkAutoLogout(PRODUCT_ID, TIME_OUT, false, WEB_ROOT.'/logout/');
        //auto logout ------End\      

	                         
	}
	else if (strcmp('login', $MOD_ID)==0) {
        //if load login page and login already, redirect to home page 
        if (WebSession::get(PRODUCT_ID, 'pass')==1 ) {
            header('Location: '.WEB_ROOT.'/');
            exit();
        }
	}
    else {
        //load modules within $ESCAPE_MOD_ID         
    }	
}
else {
    if(empty($MOD_ID) || !in_array($MOD_ID, $ESCAPE_MOD_ID)) {
        header('Location: '.WEB_ROOT.'/login/');
        exit();         
    }
}


?>