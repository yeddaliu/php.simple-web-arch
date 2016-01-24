<?php 
/*============================
 * Include files
 *    require_once()
 *    include_once(), etc...
 *===========================*/ 
require_once(UTILS_ROOT."/utils_data_filter.php");
require_once(DBMODEL_ROOT."/class.contact.php");

/*============================
 * Public Variables
 *===========================*/
$pageCurrent = webDataFilter('g','page','int');
if(empty($pageCurrent)) $pageCurrent = 1;

$pageLimit = webDataFilter('g','pagerow','int');
if(empty($pageLimit)) $pageLimit = WEB_PAGE_RECORDS;

$data = array(
    'page'=>0,  //current page
    'total'=>0, //total pages
    'records'=>0,   //total record count (all pages)
    'rows'=>array(), //record detail
    'error'=>array(    //show up when error occurs
        'errcode'=>0,
        'errmsg'=>''
    )
);
$pageTotal = 1;

/*============================
 * Public Functions
 *===========================*/
/*============================
 * Main execution
 *===========================*/
$modelContact = new AlertContact();

$result_total = $modelContact->getContactCount(WebSession::get(PRODUCT_ID, 'tno')); 
if ($result_total['success']===true) {
    $count = $result_total['data'];    
    unset($result_total);
    
    if($count > 0) {
        $pageTotal = ceil($count/$pageLimit);
    }    
    if ($pageCurrent > $pageTotal) { $pageCurrent = $pageTotal; };

    $start = ($pageLimit*$pageCurrent) - $pageLimit; // do not put $limit*($page - 1)

    $result_list = $modelContact->getContactList(WebSession::get(PRODUCT_ID, 'tno'), $start, $pageLimit); 
    if ($result_list['success']===true) {
        foreach ($result_list['data'] as $k=>$v) {          
            $data['rows'][] = array(
                'id'=>$v['contact_no'],
                'cell'=>array(
                    'name'=>$v['name'],
                    'email'=>$v['email'],  
                    'mobile_info'=> $v['mobile_info']
                )
                
            );                  
        }

        $data['page']=$pageCurrent;
        $data['total']=$pageTotal;
        $data['records']=$count;
    }
    else {      
        $data['error'] = array(
            'errcode' => $result_list['errcode'],
            'errmsg' => $result_list['errmsg']          
        );
                
    }
}
else {
    $data['error'] = array(
        'errcode' => $result_total['errcode'],
        'errmsg' => $result_total['errmsg']          
    );
}

echo json_encode($data);
/*============================
 * View Loading
 *===========================*/
?>   