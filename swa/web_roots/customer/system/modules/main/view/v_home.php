<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en-US">
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title><?php echo $view['title']; ?></title>
    <link rel="shortcut icon" href="<?php echo WEB_ROOT;?>/favicon.ico">    
    <link rel="stylesheet" href="<?php echo WEB_CSS_ROOT;?>/themes/<?php echo WEB_THEMES;?>/jquery-ui-1.8.5.custom.css" media="screen" />
    <link rel="stylesheet" href="<?php echo WEB_JS_ROOT;?>/uploadify/css/uploadify.css" media="screen" />    
    <link rel="stylesheet" href="<?php echo WEB_JS_ROOT;?>/cluetip/css/jquery.cluetip.css" media="screen" />
    <link rel="stylesheet" href="<?php echo WEB_JS_ROOT;?>/colorbox/css/colorbox.css" media="screen" />  
    <link rel="stylesheet" href="<?php echo WEB_JS_ROOT;?>/commtiva/cmtvGrid/css/cmtvGrid.css" media="screen" />  
    <link rel="stylesheet" href="<?php echo WEB_CSS_ROOT;?>/style-override.css" media="screen" />
    <link rel="stylesheet" href="<?php echo WEB_CSS_ROOT;?>/style.css" media="screen" />
    <!-- Lib Script -->
    <script type="text/javascript" src="<?php echo WEB_JS_ROOT;?>/jQuery/jquery-1.4.2.min.js"></script> 
    <script type="text/javascript" src="<?php echo WEB_JS_ROOT;?>/jQuery/jquery-ui-1.8.5.custom.min.js"></script>    
    <script type="text/javascript" src="<?php echo WEB_JS_ROOT;?>/jQuery/jquery-validate-1.7.min.js" ></script> 
    <script type="text/javascript" src="<?php echo WEB_JS_ROOT;?>/uploadify/swfobject.js"></script>
    <script type="text/javascript" src="<?php echo WEB_JS_ROOT;?>/uploadify/jquery.uploadify.v2.1.0.min.js"></script>
    <script type="text/javascript" src="<?php echo WEB_JS_ROOT;?>/colorbox/jquery.colorbox-min.js"></script>
    <script type="text/javascript" src="<?php echo WEB_JS_ROOT;?>/cluetip/jquery.cluetip.min.js" ></script>
    <script type="text/javascript" src="<?php echo WEB_JS_ROOT;?>/commtiva/cmtvGrid/jquery.tmpl.min.js" ></script>
    <script type="text/javascript" src="<?php echo WEB_JS_ROOT;?>/commtiva/cmtvGrid/jquery.cmtvGrid.js"></script>    
    <script type="text/javascript" src="<?php echo WEB_JS_ROOT;?>/jquery.form.js" ></script>
    <!-- App Script -->
    <script type="text/javascript" src="<?php echo WEB_JS_ROOT;?>/common.js"></script>   
    <script type="text/javascript">
    var WEB_ROOT = '<?php echo WEB_ROOT;?>';
    var WEB_JS_ROOT = '<?php echo WEB_JS_ROOT;?>';
    var WEB_IMG_ROOT = '<?php echo WEB_IMG_ROOT;?>';
    var WEB_CSS_ROOT = '<?php echo WEB_CSS_ROOT;?>';
    var WEB_CONTENT_ID = '<?php echo WEB_JS_CONTENT_ID;?>';
    var WEB_MSGBOX_ID = '<?php echo WEB_JS_MSGBOX_ID;?>';
    var WEB_ERRMSGBOX_ID = '<?php echo WEB_JS_ERRMSGBOX_ID;?>';
    var WEB_OKMSGBOX_ID = '<?php echo WEB_JS_OKMSGBOX_ID;?>';
    var SITE_CURRENT_TABID = 0;
    var SMS_PROGRESS_OPEN = false;
<?php 
if (WebSession::get(PRODUCT_ID, 'op')==WEB_APP_TYPE && WebSession::get(PRODUCT_ID, 'subacc')=='0') {        
    echo <<<EOF
    $(window).load(function() {
        $("#swa-content").css("min-height", $('#menu').height()+4);
    });        
EOF;
}
?>
        
    $(document).ready(function() {
        /* definitions */
        $('.menu-item').click(function (){
            var targetID = $(this).attr('id');
            if (targetID=='logout') {
                location.href=WEB_ROOT+'/logout/';
            }
            else if (targetID=='home') { 
                    //do nothing 
            }
            else {
                loadContent(WEB_ROOT+'/'+targetID+'/', $('#swa-content'));    
            }
        });       

        var errBox = getEmptyBox(WEB_ERRMSGBOX_ID);
        errBox.dialog("option", "title", "<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.dialog.err.title'); ?>");
        var successBox = getEmptyBox(WEB_OKMSGBOX_ID);
        successBox.dialog("option", "title", "<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.dialog.title'); ?>");

        // sms progress     
        $("#swa-sms-content #progress-bar").progressbar({value: 100});
        
        /* init action */        
        $('#homeIcon').click();

    });         
    </script>  
</head>
<body>    
    <div id="swa-container">        
        <div id="swa-header">
            <!-- prod's logo & branding-->            
            <div id="prod-logo"><i id="bracing"></i><img src="<?php echo $view['header']['logo'];?>"/></div>
            <div id="prod-branding"><i id="bracing"></i><?php echo $view['header']['brand'];?></div>            
            <div id="fnc"></div>
        </div>
        <div class="clear"></div>          
        <table id="swa-body" cellspacing="0" cellpadding="0" border="0"><tr>
        <td id="swa-body-left">
            <div id="swa-nav" >
                <div id="menu" class="ui-menu">
                    <div class="menu-item" id="main"><div id="homeIcon"></div></div>
                    <div class="menu-item" id="site"><div id="siteIcon"></div></div>
                    <div class="menu-item" id="account"><div id="accountIcon"></div></div>
                    <div class="menu-item" id="search"><div id="searchIcon"></div></div>
                    <div class="menu-item" id="contact"><div id="contactIcon"></div></div>
<?php 
if (WebSession::get(PRODUCT_ID, 'op')==WEB_APP_TYPE && WebSession::get(PRODUCT_ID, 'subacc')=='0') {
    echo <<<EOF
                    <div class="menu-item" id="manager"><div id="acctmgrIcon"></div></div>
EOF;
}
?>                    
                    <div class="menu-item" id="logout"><div id="logoutIcon"></div></div>
                </div>  
                <!-- <div id="widget"></div> -->
            </div>               
        </td>
        <td id="swa-body-right"><div id="swa-content"></div></td>        
        </tr></table>        
        <div class="clear"></div> 
        <div id="swa-footer">
            <div class="cr"><?php echo $view['footer']; ?></div>
        </div>      
    </div>
    
    <div id="swa-sms-progress">
        <div id="swa-sms-content">
            <div id="progress-msg"></div>
            <div id="progress-bar"></div>        
        </div>
    </div>
</body>
</head>
</html>