<link rel="stylesheet" href="<?php echo WEB_CSS_ROOT;?>/contact.css?<?php echo time();?>" media="screen" />

<script>
$(function() {
    <?php 
    if (!empty($view['jsErrMsg'])) {
        printHTMLContent('genJSExistBox', array(WEB_JS_ERRMSGBOX_ID, $view['jsErrMsg']));     	
        unset($view['jsErrMsg']);
    }             
    ?>

    $("#contactSetting #submit").button().click(function() { 
        var emailType = '';
        var mobileType = '';
        $("input:checkbox[name='email[]']").each(function() {
            if ($(this).attr("checked")){
                if (emailType.length>0) {
                	emailType +=  ',';
                }
              	emailType += $(this).attr("value");
            }
        });
        $("input:checkbox[name='mobile[]']").each(function() {
            if ($(this).attr("checked")){
                if (mobileType.length>0) {
                	mobileType += ',';
                }
                mobileType += $(this).attr("value");
            }
        });        
        
        var data = {
        		ctno        : $("#ctno").val(),     		
                emailTypes   : emailType,
                mobileTypes  : mobileType
        }
        loadPostResult(WEB_ROOT+'/setContactAlert/ctno='+<?php echo $view['ctno'];?>, data)
        return false;         
        
    });        
    $("#contactSetting #cancel").button().click(function() { 
        loadContent(WEB_ROOT+'/contact/'); 
        return false;         
        
    });      

    showTip1('aType');    
});
</script>
<div id="contactSetting" class="embedded-block">
    <fieldset width="100%" class="ui-corner-all">            
    <legend><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.legend'); ?></legend>
        <!-- <div class="left"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.info'); ?></div> -->  
        <div class="clear"></div>        
	    <div id="overview-table"> 
	        <div > 
	            <table style="width: 100%;">
	                <thead>
	                    <tr>
	                        <th id="aTypeHead"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.head'); ?></th>
	                        <th id="aType1" class="aType ui-widget-header" tip="<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.title.1'); ?>|<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.desc.1'); ?>"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.title.1'); ?><div class="ui-icon ui-icon-info info right" ><div></th>	                       
	                        <th id="aType2" class="aType ui-widget-header" tip="<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.title.2'); ?>|<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.desc.2'); ?>"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.title.2'); ?><div class="ui-icon ui-icon-info info right" ><div></th>
	                        <th id="aType3" class="aType ui-widget-header" tip="<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.title.3'); ?>|<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.desc.3'); ?>"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.title.3'); ?><div class="ui-icon ui-icon-info info right" ><div></th>
	                        <!-- 
	                        <th id="aType4" class="aType ui-widget-header" tip="<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.title.4'); ?>|<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.desc.4'); ?>"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.title.4'); ?><div class="ui-icon ui-icon-info info right" ><div></th>                       
	                        <th id="aType5" class="aType ui-widget-header" tip="<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.title.5'); ?>|<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.desc.5'); ?>"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.title.5'); ?><div class="ui-icon ui-icon-info info right" ><div></th>
	                         -->
	                        <th id="aType6" class="aType ui-widget-header" tip="<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.title.6'); ?>|<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.desc.6'); ?>"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.title.6'); ?><div class="ui-icon ui-icon-info info right" ><div></th>
	                        <th id="aType7" class="aType ui-widget-header" tip="<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.title.7'); ?>|<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.desc.7'); ?>"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.type.title.7'); ?><div class="ui-icon ui-icon-info info right" ><div></th>                       
	                    </tr>
	                </thead>
	                <tbody>
	                    <tr>
	                        <th style="text-align: center;"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.email'); ?></th>
	                        <td>
	                        <div class="field-v">
	                        <div class="field-input">
	                            <input type="checkbox" id="email" name="email[]" class="text ui-widget-content ui-corner-all" value="0" <?php echo $view['emailCheck'][0]; ?>/>
	                        </div>                                                    
	                        </div>
	                        </td>
	                        <td>
	                        <div class="field-v">
	                        <div class="field-input">
	                            <input type="checkbox" id="email" name="email[]" class="text ui-widget-content ui-corner-all" value="1" <?php echo $view['emailCheck'][1]; ?>/>
	                        </div>                                                    
	                        </div>
	                        </td>
	                        <td>
	                        <div class="field-v">
	                        <div class="field-input">
	                            <input type="checkbox" id="email" name="email[]" class="text ui-widget-content ui-corner-all" value="2" <?php echo $view['emailCheck'][2]; ?>/>
	                        </div>                                                    
	                        </div>
	                        </td>
	                        <!-- 
	                        <td>
	                        <div class="field-v">
	                        <div class="field-input">
	                            <input type="checkbox" id="email" name="email[]" class="text ui-widget-content ui-corner-all" value="3" <?php echo $view['emailCheck'][3]; ?>/>
	                        </div>                                                    
	                        </div>
	                        </td>
	                        <td>
	                        <div class="field-v">
	                        <div class="field-input">
	                            <input type="checkbox" id="email" name="email[]" class="text ui-widget-content ui-corner-all" value="4" <?php echo $view['emailCheck'][4]; ?>/>
	                        </div>                                                    
	                        </div>
	                        </td>
	                         -->
	                        <td>
	                        <div class="field-v">
	                        <div class="field-input">
	                            <input type="checkbox" id="email" name="email[]" class="text ui-widget-content ui-corner-all" value="5" <?php echo $view['emailCheck'][5]; ?>/>
	                        </div>                                                    
	                        </div>
	                        </td>
                            <td>
                            <div class="field-v">
                            <div class="field-input">
                                <input type="checkbox" id="email" name="email[]" class="text ui-widget-content ui-corner-all" value="6" <?php echo $view['emailCheck'][6]; ?>/>
                            </div>                                                    
                            </div>
                            </td>
	                    </tr>
	                    <tr>
	                        <th style="text-align: center;"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.alert.mobile'); ?></th>
	                        <td>
	                        <div class="field-v">
	                        <div class="field-input">
	                            <input type="checkbox" id="mobile" name="mobile[]" class="text ui-widget-content ui-corner-all" value="0" <?php echo $view['mobileCheck'][0]; ?>/>
	                        </div>                                                    
	                        </div>
	                        </td>
	                        <td>
	                        <div class="field-v">
	                        <div class="field-input">
	                            <input type="checkbox" id="mobile" name="mobile[]" class="text ui-widget-content ui-corner-all" value="1" <?php echo $view['mobileCheck'][1]; ?>/>
	                        </div>                                                    
	                        </div>
	                        </td>
	                        <td>
	                        <div class="field-v">
	                        <div class="field-input">
	                            <input type="checkbox" id="mobile" name="mobile[]" class="text ui-widget-content ui-corner-all" value="2" <?php echo $view['mobileCheck'][2]; ?>/>
	                        </div>                                                    
	                        </div>
	                        </td>
	                        <!-- 
	                        <td>
	                        <div class="field-v">
	                        <div class="field-input">
	                            <input type="checkbox" id="mobile" name="mobile[]" class="text ui-widget-content ui-corner-all" value="3" <?php echo $view['mobileCheck'][3]; ?>/>
	                        </div>                                                    
	                        </div>
	                        </td>
	                        <td>
	                        <div class="field-v">
	                        <div class="field-input">
	                            <input type="checkbox" id="mobile" name="mobile[]" class="text ui-widget-content ui-corner-all" value="4" <?php echo $view['mobileCheck'][4]; ?>/>
	                        </div>                                                    
	                        </div>
	                        </td>
	                         -->
	                        <td>
	                        <div class="field-v">
	                        <div class="field-input">
	                            <input type="checkbox" id="mobile" name="mobile[]" class="text ui-widget-content ui-corner-all" value="5" <?php echo $view['mobileCheck'][5]; ?>/>
	                        </div>                                                    
	                        </div>
	                        </td>
                            <td>
                            <div class="field-v">
                            <div class="field-input">
                                <input type="checkbox" id="mobile" name="mobile[]" class="text ui-widget-content ui-corner-all" value="6" <?php echo $view['mobileCheck'][6]; ?>/>
                            </div>                                                    
                            </div>
                            </td>
	                    </tr>
	                </tbody>
	            </table>
	            
	        </div>                             
	    </div>
        <div style="text-align:right; padding:0 10px 0 20px;">
            <input type="hidden" id="ctno" name="ctno" value="<?php echo $view['ctno'];?>" />
            <button id="submit"><?php echo $GLOBALS['MOD_LANG']->getMessage('gl.btn.update'); ?></button>            
            <button id="cancel"><?php echo $GLOBALS['MOD_LANG']->getMessage('gl.btn.back'); ?></button>            
        </div>	    
	    <div class="clear"></div>
    </fieldset>
</div>