<?php
 /*============================
 * Include files
 *    require_once()
 *    include_once(), etc...
 *===========================*/  
require_once(UTILS_ROOT."/utils_data_filter.php");
require_once(UTILS_ROOT."/util_time.php");

require_once(DBMODEL_ROOT.'/class.msg_trans_log.php');
require_once(SERVICE_ROOT."/sms/SmartLightingSendSMS.php");

/*============================
 * Public Variables
 *===========================*/
//SMS data passed by POST
$m = webDataFilter('g','m','string');
$Sender = WebSession::get(PRODUCT_ID, 'tno');

$result = array(
    'success'=> true,
    'errcode'=>0
);

$delay = 5;
//waiting for 5 munites
$maxtime = 60*5;
$maxcounts = $maxtime/$delay;  
/*============================
 * Public Functions
 *===========================*/  
/*============================
 * Main execution
 *===========================*/
$resultSMS = call_user_func($m, $Sender);
if ($resultSMS['success']===true) {

	if (APP_DEBUG_MODE=='1') {
		//return debug msg
        $result['sms']=$resultSMS['sms'];
	}
	else {		
	    //reset timeout limit
	    set_time_limit($maxtime);
	    
	    $modelTrans = new MsgTransLog(); 
	    $tranID = &$resultSMS['id']; 
	    $cnt = 0;
	     
	    //waiting for reply
	    $isReply = false;
	    while ($cnt++ <= $maxcounts) {
	        $resultTrans = $modelTrans->isTransReplyFromUnit($tranID);
	        if ($resultTrans['success']===false) {
	            //interrupt loop
	            $cnt += $maxtimes;
	            $result = $resultTrans; 
	            break; 
	        }       
	        
	        if ($resultTrans['data']===true) {
	            //interrupt loop
	            $cnt += $maxtimes;
	            $isReply = true;
	            break;
	        }
	        
	        sleep($delay);
	    }
	    
	    //release timeout setting
	    set_time_limit(1);
	    
	    if ($isReply === false) {
	        $result = array(
	            'success'=> false,
	            'errcode'=>'9999',
	            'errmsg'=>$GLOBALS['MOD_LANG']->getMessage('gl.sms.progress.err.timeout')
	        );          
	    }		
	}    
}
else {
	$result = &$resultSMS;
}

echo json_encode($result);
?>