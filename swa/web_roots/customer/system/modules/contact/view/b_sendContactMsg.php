<link rel="stylesheet" href="<?php echo WEB_CSS_ROOT;?>/contact.css" media="screen" />
<script>
$(function() {
    <?php 
    if (!empty($view['jsErrMsg'])) {
        printHTMLContent('genJSExistBox', array(WEB_JS_ERRMSGBOX_ID, $view['jsErrMsg']));        
        unset($view['jsErrMsg']);
    }       
    if (!empty($view['jsMsg'])) {
        printHTMLContent('genJSExistBox', array(WEB_JS_OKMSGBOX_ID, $view['jsMsg']));                   
        unset($view['jsMsg']);
    }     
    ?>

    var validator = $("#contactSendTest #contactTestForm").validate({   
        onsubmit: false,      
        ignore: "ignore",         
        errorElement: "div",    
        errorClass: "ui-state-error",
        //focusCleanup: true, 
        //force to check each field, include blnak field
        onfocusout: function(element) {
            var testResult = this.element(element);
        
            if (typeof testResult=="undefined") { }        
            else if (testResult==true) {
                if (element.name == "mobileArea" || element.name == "mobile" ) {
                    if (!$("#mobileArea").hasClass("ui-state-error") && !$("#mobile").hasClass("ui-state-error")) {
                        $("#mobile").nextAll(".field-error").remove();                       
                    }  
                }
                else {                        
                   //valid field
                   $(element).nextAll(".field-error").remove();
                }
            }            
            else {
                //$(element).focus();               
            }                        
        },
        rules: {            
            name: {
                required:   true,
                maxlength:  40
            },
            email: {
                email:      true,
                maxlength:  40
            },
            mobileArea:{
                digits:     true,
                maxlength:  4
            },
            mobile:{
                digits:     true,
                maxlength:  15
            },
            msg: {
                required:   true,
                maxlength:  65
            },
        },            
        messages: {            
            name: {
                required:   '<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.required'); ?>',
                maxlength:  jQuery.format('<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.maxlength'); ?>')
            },
            email: {
                email:      '<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.email'); ?>',
                maxlength:  jQuery.format('<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.maxlength'); ?>')
            },
            mobileArea:{
                digits:     '<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.digit'); ?>',
                maxlength:  jQuery.format('<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.maxlength'); ?>')
            },
            mobile:{
                digits:     '<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.digit'); ?>',
                maxlength:  jQuery.format('<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.maxlength'); ?>')
            },
            msg: {
                required:   '<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.required'); ?>',
                maxlength:  jQuery.format('<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.maxlength'); ?>')
            }            
        },
        errorPlacement: function(error, element) {          
            error.removeClass("ui-state-error").addClass("field-error");             
            if (element.attr("name") == "mobileArea" || element.attr("name") == "mobile") {
                if ($("#mobile").next(".field-error").length>0) {
                    $("#mobile").next(".field-error").remove();                    
                }
                error.insertAfter("#mobile");
            }  
            else {                
                if (element.next(".field-error").length>0) {
                    element.next(".field-error").remove();
                }
                error.insertAfter(element);
            }
            
        }        
        /*highlight: function(element, errorClass) {
            $(element).addClass(errorClass).fadeOut(function() {
                $(element).fadeIn();
            });
        },   
        invalidHandler: function(form, validator) {},      
              
        submitHandler: function(form) {            
            var type = $("#sendAct").val();
            var ctno = $("#ctno").val();
            
            var data = $(form).formSerialize();             
            if (type=="add") {
                loadPostResult(WEB_ROOT+'/setContact/act=add', data, $('#swa-content'));
            }
            else {
                loadPostResult(WEB_ROOT+'/setContact/act=modify&ctno='+ctno, data, $('#swa-content'));              
            }
        }
        */ 
        
    });  
    
    $("#contactSendTest #btnSendContactSMS").button().click(function() { 
        //check mobile

        var qString = $('#contactTestForm').formSerialize(); 
        qString += '&sendAct=sms';        
        loadPostResult(WEB_ROOT+'/testContact/', qString, $('#swa-content'))
        return false; 
        
    });
    $("#contactSendTest #btnSendContactEmail").button().click(function() {    
        //check email
            
        var qString = $('#contactTestForm').formSerialize(); 
        qString += '&sendAct=email';
        loadPostResult(WEB_ROOT+'/testContact/', qString, $('#swa-content'))
        return false;         
    });   
    $("#contactSendTest #btnSendContactAll").button().click(function() { 
        var qString = $('#contactTestForm').formSerialize(); 
        qString += '&sendAct=all';       
        loadPostResult(WEB_ROOT+'/testContact/', qString, $('#swa-content'))
        return false;                 
    });
    
    $("#contactSendTest #cancel").button().click(function() { 
        loadContent(WEB_ROOT+'/contact/', $('#swa-content')); 
        return false;         
        
    });  
});
</script>
<div id="contactSendTest" class="embedded-block">
    <fieldset width="100%" class="ui-corner-all">            
    <legend><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.test.legend'); ?></legend>
        <form id="contactTestForm" name="contactTestForm"> 
        <div class="field">
            <label><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.test.label.name'); ?></label>
            <div class="field-input">
                <input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all"
                        value="<?php echo $view['testContact']['formData']['name']; ?>">  
                <!-- &#160;<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.test.input.note1'); ?> -->                        
            </div>
        </div> 
        <div class="clear"></div>   
        <div class="field">
            <label><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.test.label.email'); ?></label>
            <div class="field-input">
                <input type="text" id="email" name="email" class="text ui-widget-content ui-corner-all" style="width:400px;" 
                        value="<?php echo $view['testContact']['formData']['email']; ?>">   
                <!-- &#160;<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.test.input.note1'); ?> -->                             
            </div>            
        </div> 
        <div class="clear"></div>               
        <div class="field">
            <label><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.test.label.mobile'); ?></label>
            <div class="field-input">
                (<input type="text" id="mobileArea" name="mobileArea" class="text ui-widget-content ui-corner-all" style="width:50px;" maxlength="4" 
                        value="<?php echo $view['testContact']['formData']['mobile_area']; ?>">)
                &#160;<input type="text" id="mobile" name="mobile" class="text ui-widget-content ui-corner-all" maxlength="15"
                        value="<?php echo $view['testContact']['formData']['mobile']; ?>">                        
            </div>            
        </div> 
        <div class="clear"></div>
        <div class="field">
            <label><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.test.label.sms'); ?></label>
            <div class="field-input">
                <select id="sp" name="sp" class="text ui-widget-content ui-corner-all">
                <?php echo $view['testContact']['spOption'];?>
                </select>                  
            </div>            
        </div> 
        <div class="clear"></div>
        <div class="field">
            <label><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.test.label.msg'); ?></label>
            <div class="field-input">
                <textarea id="msg" name="msg" class="text ui-widget-content ui-corner-all" style="width:300px; height:100px;"><?php echo $view['testContact']['formData']['msg'];?></textarea>
                <!-- &#160;<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.test.input.note2'); ?> -->                  
            </div>            
        </div> 
        <div class="clear"></div>
        </form>
        <div class="left" style="margin-left:150px;padding-left:10px;">
            <button id="btnSendContactSMS"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.test.btn.sms'); ?></button>
            <button id="btnSendContactEmail"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.test.btn.email'); ?></button>
            <button id="btnSendContactAll"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.test.btn.all'); ?></button>
        </div>        
    </fieldset>
    <div class="right">
        <button id="cancel"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.test.btn.back'); ?></button>        
    </div>        

</div>