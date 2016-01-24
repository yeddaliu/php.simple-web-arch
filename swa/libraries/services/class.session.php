<?php
class WebSession{

	public static function get($prodid, $key=null){
		
		return (empty($key)) ? (isset($_SESSION[$prodid])? $_SESSION[$prodid]:null)
		                     : ((isset($_SESSION[$prodid][$key]))? $_SESSION[$prodid][$key]:null);
	}

	public static function put($prodid,$key,$value){
		$previous = null;
		if (isset($_SESSION[$prodid]) && isset($_SESSION[$prodid][$key])) {
		  $previous = $_SESSION[$prodid][$key];	
		}
		
		$_SESSION[$prodid][$key] = $value;
		return $previous;
	}

	public static function destroy($prodid){		
		if (isset($_SESSION[$prodid])) {
    		unset($_SESSION[$prodid]);
		
	   	    //force cookie expired		
            $cookie_dir = WEB_ROOT.'/';
            setcookie(session_name(WEB_SESSION_NAME), session_id(), time()-3600, $cookie_dir);
		}
    }

	/*session config reset (before session_start)*/
	public static function beforeStart($prodid){		
        if (!isset($_SESSION[$prodid])) {
            $_SESSION[$prodid] = array();

            //declare session name of app first
            session_name(WEB_SESSION_NAME);
            $cookie_dir = WEB_ROOT.'/';     
            //set timeout when close browser
            session_set_cookie_params (0, $cookie_dir);                     
        } 
                        
	}
		
	public static function checkAutoLogout($prodid, $sec, $isDestroySession=true, $return_url=null){
		
		if(is_numeric($sec)){
			$leaveTime = self::get($prodid, 'leaveTime');
			$nowSec = time();							
			if(empty($leaveTime) || ($leaveTime > $nowSec)){
				self::put($prodid, 'leaveTime', ($nowSec+$sec) );
				return true;
			}
			else{
				if ($isDestroySession===true) {
                    self::destroy($prodid);      				
				}
				if (empty($return_url)) {
					return false;
				}
				else {
					header('location:'.$return_url);
					exit();
				}
			}
		}
		return false;
	}

}

?>