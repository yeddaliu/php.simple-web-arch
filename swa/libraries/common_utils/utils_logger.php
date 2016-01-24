<?php
/*============================
 * Include files
 *    require_once()
 *    include_once(), etc...
 *===========================*/ 

/*============================
 * Public Variables
 *===========================*/
if (defined('UTILS_LOGGER')) return ;
define ('UTILS_LOGGER',1) ;

/*============================
 * Public Functions
 *===========================*/
/*===========================================================================
 * Function Name : logHelper
 * Description : Help to generate a nuniform application message log
 * Input : $msg 	 => log message
		   $title    => log title, might be short description about the message
		   $logFile	 => full path of the log file
 * Output : none
 * Return : if logged, retrun TRUE
 * Side effects : none
 *=========================================================================*/  
function logHelper($msg, $title, $logFile) {
    if (empty($logFile)) return false;  
    $logger_msg = date("Y-m-d H:i:s").' '.(empty($title)?'':"[{$title}] ").$msg."\n";  
    error_log($logger_msg, 3, $logFile);
    return true;
}



?>