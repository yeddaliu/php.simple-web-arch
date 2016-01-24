<?php
/*============================
 * Include files
 *    require_once()
 *    include_once(), etc...
 *===========================*/ 

/*============================
 * Public Variables
 *===========================*/
if (defined('UTILS_DATA_FILTER')) return ;
define ('UTILS_DATA_FILTER',1) ;

/*============================
 * Public Functions
 *===========================*/
/*===========================================================================
 * Function Name : webDataFilter
 * Description :  GET/POST data should be filtered
 * Input : $method 	 => 'g','get','p','post'
		   $variable =>  the variable name 
		   $type	 => 'int','float','string','mail','defalut'
 * Output : none
 * Return : $variable => the clear variable
 * Side effects : none
 *=========================================================================*/  
function webDataFilter($method_index,$variable,$data_index){

	$method_type = array(
		'g'		=>	INPUT_GET,
		'p'		=>	INPUT_POST,
		'get'	=>	INPUT_GET,
		'post'	=>	INPUT_POST,
	);
	
	$data_type = array(
		'int'		=>	FILTER_SANITIZE_NUMBER_INT,
		'float'		=>	FILTER_SANITIZE_NUMBER_FLOAT,
		'string'	=>	FILTER_SANITIZE_STRIPPED,
		'mail'		=>	FILTER_SANITIZE_EMAIL,
		'email'		=>	FILTER_SANITIZE_EMAIL,
		'defalut'	=>	FILTER_SANITIZE_STRIPPED,
	);
	
    
	$method = $method_type[$method_index];
	$type = $data_type[$data_index];    
    
    if($data_index == 'array'){
        $args = array(
            $variable => array(
                'name' => $variable,
                'filter' => FILTER_SANITIZE_STRING,
                'flags'  => FILTER_REQUIRE_ARRAY
            )
        );            
        return array_shift(filter_input_array($method,$args));
    }


	if(isset($method) and isset($type)) return trim(filter_input($method,$variable,$type));
	else return NULL;
	
}

/*===========================================================================
 * Function Name : getSLTAcceptedFileMap
 * Description : help to filter out unaccept uploaded files
 * Input : none
 * Output : none
 * Return : $AcceptedFileMap => seperated in images and file group
 * Side effects : none
 *=========================================================================*/  
function getSLTAcceptedFileMap() {
	$AcceptedFileMap = array(
	   'image' => array(
		    'png'=>'image/png', 
		    'jpg'=>'image/jpeg', 
		    'jpeg'=>'image/jpeg', 
            'gif'=>'image/gif',	
	   ),
	   'file'  => array(
            'pdf'=>'application/pdf', 	   
	   )	
	);
	return $AcceptedFileMap;	
}

/*===========================================================================
 * Function Name : uploadedFileFilter
 * Description : filter out unaccepted file based on file extension name
 * Input : $fileName => target file name
 * Output : none
 * Return : file's mime type
 * Side effects : none
 *=========================================================================*/  
function uploadedFileFilter($fileName){
    $file_ext = str_replace('.','',strtolower(strrchr($fileName,'.')));
	
    $fileMap = &getSLTAcceptedFileMap();
	$fileTypeMap = array_merge($fileMap['image'],$fileMap['file']);
	if (isset($fileTypeMap[$file_ext]) && !empty($fileTypeMap[$file_ext])) {
		return $fileTypeMap[$file_ext];
	}
	else return null;
}

/*===========================================================================
 * Function Name : isImage
 * Description : check if target file is image or not based on file extension name
 * Input : $fileName => target file name
 * Output : none
 * Return : if image, return TRUE
 * Side effects : none
 *=========================================================================*/  
function isImage($fileName){
    $file_ext = str_replace('.','',strtolower(strrchr($fileName,'.')));
    $fileMap = &getSLTAcceptedFileMap();    
    
    return array_key_exists($file_ext, $fileMap['image']);    
}



?>