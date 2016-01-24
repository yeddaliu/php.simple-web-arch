<?php
/** html **/
function printHTMLContent($fncName, $paramsArray) {
	if (is_callable($fncName)===true ) {
		echo call_user_func_array($fncName, $paramsArray);
	}
}

function genErrorModuleHTML($framename, $title, $msg, $zoneClass='embedded-block') {
	
    $content =  <<<EOF
	<div id="errorZone" class="{$zoneClass}">
	    <fieldset width="100%" class="ui-corner-all">            
	    <legend>{$framename}</legend>            
	        <div class="title">{$title}</div> 
	        <div class="content">{$msg}</div>  
	    </fieldset>
	    <div class="clear"></div>
	</div>	
EOF;
	
    return $content;
}

function genJSExistBox($divID, $msg) {
    if (empty($divID) || empty($msg)) return ''; 

    $content = <<<EOF
    $('#{$divID}')
        .html('{$msg}')
        .dialog('open');    
            
EOF;
        
    return $content;
}

function genJSExistBoxWithCloseRedirect($divID, $msg, $url='', $zoneClass='') {
    $actionScript = '';
    if (!empty($url)) {
    	$actionScript = empty($zoneClass)? "loadContent('{$url}')":"loadContent('{$url}', $('#'+{$zoneClass}))";
    }
    
    return genJSExistBoxWithCloseScript($divID, $msg, $actionScript);
}


function genJSExistBoxWithCloseScript($divID, $msg, $actionScript) {
    $dialogScript = genJSExistBox($divID, $msg);
    if (empty($dialogScript)){
        return '';
    }
    
    $content = <<<EOF
    //$("#{$divID}").unbind("dialogclose"); 
        
    {$dialogScript}    
 
    $("#{$divID}").bind("dialogclose", function(event, ui) {
        {$actionScript}
        $("#{$divID}").unbind("dialogclose");
    });    
        
EOF;
        
    return $content;
}

function genJSDialogBox($objID='dialog', $title='', $msg) {
	if (empty($msg)) return ''; 

    $content = <<<EOF
    var {$objID}Box = getEmptyBox();
    {$objID}Box.dialog("option", "title", "{$title}");
    
    {$objID}Box
        .html('{$msg}')
        .dialog('open');    
            
EOF;
        
    return $content;
}

function genJSDialogBoxWithCloseRedirect($objID='dialog', $title='', $msg, $url='', $zoneClass='') {
    $actionScript = '';
    if (!empty($url)) {
        $actionScript = empty($zoneClass)? "loadContent('{$url}')":"loadContent('{$url}', $('#'+{$zoneClass}))";
    }
	
    return genJSDialogBoxWithCloseScript($objID, $title, $msg, $actionScript);
}

function genJSDialogBoxWithCloseScript($objID='dialog', $title='', $msg, $actionScript) {
    $dialogScript = genJSDialogBox($objID, $title, $msg);
    if (empty($dialogScript)){
    	return '';
    }
    
    $content = <<<EOF
    {$dialogScript}    
      
    {$objID}Box.bind("dialogclose", function(event, ui) {
        {$actionScript}
    });    
        
EOF;
        
    return $content;
}

function genStandalongDialogBox($objID='dialog', $title='', $msg) {
    $dialogScript = genJSDialogBox($objID, $title, $msg);
	
    $content = <<<EOF
    <script type="text/javascript">
    $(function() {
    {$dialogScript}    
    });         
    </script>      
EOF;

    return $content;
}

function genStandalongJSBlock($scriptContent) {
		
    $content = <<<EOF
    <script type="text/javascript">
    $(function() {
    {$scriptContent}    
    });         
    </script>      
EOF;

    return $content;
}

?>