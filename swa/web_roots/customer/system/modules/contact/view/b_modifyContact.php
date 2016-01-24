<link rel="stylesheet" href="<?php echo WEB_CSS_ROOT;?>/contact.css" media="screen" />
<script>
$(function() {
    <?php 
    if (!empty($view['jsErrMsg'])) {
        printHTMLContent('genJSExistBox', array(WEB_JS_ERRMSGBOX_ID, $view['jsErrMsg']));        
        unset($view['jsErrMsg']);
    }             
    ?>

    $("#contactModify #submit").button();    
    $("#contactModify #cancel").button().click(function() { 
        loadContent(WEB_ROOT+'/contact/', $('#swa-content')); 
        return false;         
        
    });      
});
</script>
<script><?php require_once(PRODUCT_MODULES_ROOT."/contact/view/js/contactValidate.js");?></script>
<div id="contactModify" class="embedded-block">
    <fieldset width="100%" class="ui-corner-all">            
    <legend><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.modify.legend'); ?></legend>
        <form method="POST" id="contactForm" name="contactForm">
        <div class="field">
            <label><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.label.name'); ?></label>
            <div class="field-input">
                <input type="text" id="name" name="name" class="text ui-widget-content ui-corner-all"
                        value="<?php echo $view['setContact']['formData']['contact_name']; ?>">                    
            </div>
        </div> 
        <div class="clear"></div>   
        <div class="field">
            <label><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.label.email'); ?></label>
            <div class="field-input">
                <input type="text" id="email" name="email" class="text ui-widget-content ui-corner-all" style="width:400px;" 
                        value="<?php echo $view['setContact']['formData']['contact_email']; ?>">                        
            </div>            
        </div> 
        <div class="clear"></div>               
        <div class="field">
            <label><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.label.mobile'); ?></label>
            <div class="field-input">
                (<input type="text" id="mobileArea" name="mobileArea" class="text ui-widget-content ui-corner-all" style="width:50px;" maxlength="4" 
                        value="<?php echo $view['setContact']['formData']['contact_mobile_area']; ?>">)
                &#160;<input type="text" id="mobile" name="mobile" class="text ui-widget-content ui-corner-all" maxlength="15"
                        value="<?php echo $view['setContact']['formData']['contact_mobile']; ?>">                        
            </div>            
        </div> 
        <div class="clear"></div>
        <div class="field">
            <label><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.label.sms'); ?></label>
            <div class="field-input">
                <select id="sp" name="sp" class="text ui-widget-content ui-corner-all">
                <?php echo $view['setContact']['spOption'];?>
                </select>                  
            </div>            
        </div> 
        <div class="clear"></div>
        <div class="right">
            <input type="hidden" id="sendAct" name="sendAct" value="modify" />
            <input type="hidden" id="ctno" name="ctno" value="<?php echo $view['setContact']['ctno'];?>" />
            <button id="submit"><?php echo $GLOBALS['MOD_LANG']->getMessage('gl.btn.update'); ?></button>
            <button id="cancel"><?php echo $GLOBALS['MOD_LANG']->getMessage('gl.btn.back'); ?></button>
        </div>
        
        </form>
    </fieldset>
</div>