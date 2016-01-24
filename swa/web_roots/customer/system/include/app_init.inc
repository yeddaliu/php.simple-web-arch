<?php
/*===========================================================================
 *
 * Filename : app_init.inc
 * Module Prefix : CUST/include/
 * Author: Yedda Liu
 * Created on : Nov. 07, 2010
 * Description : Customer Level Program Initial Handler
 * FileType : UTF-8
 * Copyright 2010 Commtiva Technology. All rights reserved.
 *
 * $Id: app_init.inc,v 1.13 2010-07-01 08:06:14 yedda Exp $
 *
 * $Log: app_init.inc,v $
 * 
 *=========================================================================*/
//print_r($_SERVER); 

/*LOCAL DEFINITION*/
require_once('app_definition.inc');

/*GLOBAL DEFINITION*/
require_once(PRJ_LIB_ROOT.'/global_define/global_init.inc');
/*GLOBAL OVERRIDE*/
//define('TIME_OUT','60');

/*WEB COMMON VAR*/
$MOD_ID = empty($_SERVER['PATH_INFO'])? '': str_replace('/', '', $_SERVER['PATH_INFO']);
$ESCAPE_MOD_ID = array(
        'login',
        'logout',                   
);

/*WEB PRE-LOAD LIB*/
require_once(SERVICE_ROOT.'/class.session.php');
//require_once(SERVICE_ROOT.'/proc.classAutoload.php');

/*WEB COMMON PROCESS*/
require_once(SERVICE_ROOT.'/proc.sessionSecurity.php');
?>