
$(function() {
	$("#contactAdd #contactForm, #contactModify #contactForm").validate({   
        onsubmit: true,      
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
                maxlength:  255
            },
            email: {
                required:   true,
                email:      true,
                maxlength:  255
            },
            mobileArea:{
                required:   true,
                digits:     true,
                maxlength:  4
            },
            mobile:{
                required:   true,
                digits:     true,
                maxlength:  15
            },
            sp: {
                required:   true
            }
        },            
        messages: {            
            name: {
                required:   '<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.required'); ?>',
                maxlength:  jQuery.format('<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.maxlength'); ?>')
            },
            email: {
                required:   '<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.required'); ?>',
                email:      '<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.email'); ?>',
                maxlength:  jQuery.format('<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.maxlength'); ?>')
            },
            mobileArea:{
                required:   '<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.err.mobile.required'); ?>',
                digits:     '<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.digit'); ?>',
                maxlength:  jQuery.format('<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.maxlength'); ?>')
            },
            mobile:{
                required:   '<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.err.mobile.required'); ?>',
                digits:     '<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.digit'); ?>',
                maxlength:  jQuery.format('<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.maxlength'); ?>')
            },
            sp: {
                required:   '<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.valimsg.select'); ?>'
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
            
        },        
        /*highlight: function(element, errorClass) {
            $(element).addClass(errorClass).fadeOut(function() {
                $(element).fadeIn();
            });
        },   
        invalidHandler: function(form, validator) {},      
        */      
        submitHandler: function(form) {            
            var data = $(form).formSerialize();             
            if ($("#sendAct").val()=="add") {
            	loadPostResult(WEB_ROOT+'/setContact/act=add', data, $('#swa-content'));
            }
            else {
                loadPostResult(WEB_ROOT+'/setContact/act=modify&ctno='+$("#ctno").val(), data, $('#swa-content'));            	
            }
        } 
        
    });  

});	