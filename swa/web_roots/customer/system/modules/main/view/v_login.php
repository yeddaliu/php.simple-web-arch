<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en-US">
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <title><?php echo $view['title']; ?></title>
    <link rel="shortcut icon" href="<?php echo WEB_ROOT;?>/favicon.ico">    
    <link rel="stylesheet" href="<?php echo WEB_CSS_ROOT;?>/login.css" media="screen" />
    <script>
    function populate(o) {
        I=document.getElementById('userid').value;
        P=document.getElementById('password').value;

        if (I!="" && P!="")
            document.form1.submit()
        else if (I=="") 
            alert('<?php echo $GLOBALS['MOD_LANG']->getMessage('js.err.user.empty'); ?>');
        else 
            alert('<?php echo $GLOBALS['MOD_LANG']->getMessage('js.err.pass.empty'); ?>');
    }        
    </script> 
</head>

<body>
    <div id="container">
        <div id="header">
            <table style="height:80px;">
            <tbody>
                <tr>
                    <td style="width:100%;padding:8px">
                    </td>
                </tr>
            </tbody>
            </table>
        </div>
        <div style="border:0px; width:950px; padding: 20px">    
            <table width="950" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td height="104"><img src="<?php echo WEB_IMG_ROOT;?>/login/login_topbg.jpg" width="950" height="104" /></td>
                </tr>
                <tr>
                    <td height="410" valign="top" background="<?php echo WEB_IMG_ROOT;?>/login/login_bg.jpg">
                        <form id="form1" name="form1" method="post" action="<?php echo WEB_ROOT;?>/login/" onKeyPress="if(event.keyCode == 13)this.submit()"> 
                        <table width="500" border="0" align="center" cellpadding="0" cellspacing="5">
                            <tr>
                                <td align="right"><font size="4"><?php echo $GLOBALS['MOD_LANG']->getMessage('label.user'); ?></font></td>
                                <td><input type="text" name="userid" id="userid" style="width: 200px;"/></td>
                                <td rowspan="2"><img onClick="populate(this)" src="<?php echo WEB_IMG_ROOT;?>/login/login.gif" width="68" height="68" border="0" style="cursor:hand" /></td>
                            </tr>
                            <tr>
                                <td align="right"><font size="4"><?php echo $GLOBALS['MOD_LANG']->getMessage('label.pass'); ?></font></td>
                                <td><input type="password" name="password" id="password"  style="width: 200px;" /></td>
                            </tr>
                            <tr>
			                    <td colspan="3" align="center"><?php echo $GLOBALS['MOD_LANG']->getMessage('browsing.note'); ?></td>                            
                            </tr>
                        </table>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
        <div style="height:30px; border:0px; text-align:center; font-weight:bold">
            Powered by&nbsp;<img src="<?php echo WEB_IMG_ROOT;?>/logo/logo_h30.png" alt="" align="middle" />        
        </div>
        <div style="height:5px; border:0px; text-align:center;">
            &nbsp;        
        </div>        
        <div id="footer"><?php echo $view['footer']; ?></div>  
    </div>
</body>
</html>

