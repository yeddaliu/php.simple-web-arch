<?php
$MODULE_REGISTRY = array (
/* viewport controller */
    "home" => array (
        "module" => "main",
        "control" => "home.php",
        "lang_module" => "home"
    ),
	"login" => array (
	    "module" => "main",
	    "control" => "login.php", 
        "lang_module" => "login"
    ),
    "logout" => array (
        "module" => "main",
        "control" => "logout.php", 
    ),
/* block ajax controller */    
    "sendSMS" => array (
        "module" => "main",
        "control" => "proc_sms.php", 
        "lang_module" => "home"    
    ),    
    "main" => array (
        "module" => "main",
        "control" => "main.php", 
        "lang_module" => "home"    
    ),    
    "profile" => array (
        "module" => "profile",
        "control" => "profile.php",
        "lang_module" => "home"
    )
);
?>