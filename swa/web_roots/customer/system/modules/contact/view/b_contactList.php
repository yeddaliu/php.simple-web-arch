<link rel="stylesheet" href="<?php echo WEB_CSS_ROOT;?>/contact.css" media="screen" />
<script>
$(function() {
    $("#contactOverview #btnAddContact").button().click(function() { 
        loadContent(WEB_ROOT+'/setContact/act=add', $('#swa-content')); 
        return false; 
        
    });
    $("#contactOverview #btnTestMsg").button().click(function() {        
        loadContent(WEB_ROOT+'/testContact/', $('#swa-content'));         
        return false;         
    });   

    var contactTmpl = '<tr>';
    contactTmpl += '<td>${cell.name}</td><td><p>${cell.email}</p><p>${cell.mobile_info}</p></td>';
    contactTmpl += '<td id="${id}">';
    contactTmpl += '<button id="btnModContact"><?php echo $GLOBALS['MOD_LANG']->getMessage('gl.btn.modify');?></button>';
    contactTmpl += '<button id="btnDelContact"><?php echo $GLOBALS['MOD_LANG']->getMessage('gl.btn.delete');?></button>';
    contactTmpl += '<button id="btnSetting"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.list.btn.setting');?></button>';
    contactTmpl += '</td></tr>';
    
    $("#contactOverview #overview-table").cmtvGrid({
        tableFormat:{
            //id:"contactListTable",
            //cls:"ui-widget ui-widget-content",
            header:{
                //id:"",
                //cls:"ui-widget-header",
                columns:[{
                    name:"<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.list.th.name'); ?>",   
                    cls:"contactName"
                },{
                    name:"<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.list.th.contact'); ?>",       
                    cls:"contactInfo"
                },{
                	name:"<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.list.th.fnc'); ?>",       
                    cls:"contactMgr"
                }]
            },
            bodyTemplate: contactTmpl,
            body:false,
            bodyRowCls: false,    
            bodyAltRowCls: false                         
        },
        pagerFormat:{
            style: 'float:right;',
            nav: {
                baseCls:'swa-pager-nav-base',
                maxNavNum: 5,
                first:{imageCls:false, cls:'ui-widget-header swa-pager-nav-ctrl', style:false},
                prev:{imageCls:false, cls:'ui-widget-header swa-pager-nav-ctrl', style:false},
                next:{imageCls:false, cls:'ui-widget-header swa-pager-nav-ctrl', style:false},
                last:{imageCls:false, cls:'ui-widget-header swa-pager-nav-ctrl', style:false},
                current:{cls:'ui-state-focus swa-pager-nav-current', style:false},
                list:{cls:'swa-pager-nav-list', style:false}
                            
            },   
            param:{
                page: 1,
                pagerow: <?php echo WEB_PAGE_RECORDS; ?>
            }
        },
        queryUrl: WEB_ROOT + '/getContactList/',
        mtype: "get",
        loadText: "<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.grid.loadtext');?>",
        emptyDisplay:{
            text:"<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.txt.nodata');?>"
        },
        errorDisplay: {
            text:'<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.list.load.error');?>'
        }

    }, function(){
        $("#contactOverview #btnDelContact").button().click(function() { 
        	var targetID = $(this).parent('td').attr('id');
            var msgconfirm = "<?php echo $GLOBALS['MOD_LANG']->getMessage('contact.list.txt.del');?>"; 
            
            var boxConfirm = getConfirmBox("boxDelContact", "<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.txt.confirm');?>", msgconfirm, {
                "<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.btn.confirm');?>": function() {
                	var targetID = $(this).data('target').id ;

                	//lose focus to prevent focus css
                    $($(this).data('target').btn).blur();
                    $(this).dialog("close");                                               
                    $(this).dialog("destroy");
                    $(this).remove();                                      
                    
                    var data = {
                            sendAct: 'del',
                            ctno: targetID
                    }     
                    loadPostResult(WEB_ROOT+'/setContact/act=del&ctno='+targetID, data,$('#swa-content'));         
                },
                "<?php echo $GLOBALS['MOD_LANG']->getMessage('gl.btn.cancel');?>": function() {
                    $($(this).data('target').btn).blur();
                    $(this).dialog("close");
                    $(this).dialog("destroy");
                    $(this).remove();
                }
            });        
            //pass data 
            $(boxConfirm).data('target', { "id": targetID, "btn": this });
        	
            return false;         
        });
        $("#contactOverview #btnModContact").button().click(function() { 
            var targetID = $(this).parent('td').attr('id');
            loadContent(WEB_ROOT+'/setContact/act=modify&ctno='+targetID, $('#swa-content'));         
            return false;         
        });
        $("#contactOverview #btnSetting").button().click(function() { 
            var targetID = $(this).parent('td').attr('id');
            loadContent(WEB_ROOT+'/setContactAlert/ctno='+targetID, $('#swa-content'));
            return false; 
            
        });    

    });
        
});
</script>
<div id="contactOverview" class="embedded-block">
    <fieldset width="100%" class="ui-corner-all">            
    <legend><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.list.legend'); ?></legend>
        <div id="overview-table" class="ui-widget"></div>       
        <div class="left">
            <button id="btnAddContact"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.list.btn.add'); ?></button>
            <button id="btnTestMsg"><?php echo $GLOBALS['MOD_LANG']->getMessage('contact.list.btn.test'); ?></button>            
        </div>
    </fieldset>
</div>