<?php
require_once('class.baseModel.php');

class User extends DBBaseModel {

    /*===========================================================================
     * Function Name : _addUser
     * Description : add customer info
     * Input :
     *     $creater => user's login no who create this user (might be dealers & master customers)
     *     $acc_type => account type. dealer=1; customer=2; account mgr=3
     *     $sub_acc => set '1' if this user is a sub account , else set '0' 
     *     $param = array(
     *          "login_id"=> user's login id
     *          "password"=> user's login password
     *          "first_name"=> user's first name
     *          "last_name"=> user's last name
     *          "phone_area_code"=>  user's phone
     *          "phone"=>  user's phone
     *          "phone_ext"=>  user's phone
     *          "mobile_area_code"=>  user's mobile
     *          "mobile_number"=>  user's mobile
     *          "email"=> user's email
     *          "group_name"=> user's company name, provide if is a master user
     *          "street"=> user's company location, provide if is a master user
     *          "city"=> user's company location, provide if is a master user
     *          "state"=>  user's company location, provide if is a master user
     *          "country"=>  user's company location, provide if is a master user
     *          "zip"=>  user's company location zip code, provide if is a master user
     *      );
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     *     $result['data'] => return the newly created user login no
     * Side effects : none
     *=========================================================================*/           
    private function _addUser($creater, $acc_type='2', $sub_acc='0', $param){
    	
    	//creater: admin=0
    	if (strlen($creater)==0 || preg_match('/[1|2|3]{1}/', $acc_type)==0 || preg_match('/[0|1]{1}/', $sub_acc)==0) {
                return array(
                    'success'=>false,
                    'errcode'=>'0002',        
                    'errmsg'=>$this->error->getErrorMessage('0002')                   
                );                              
    	}
    	foreach($param as $k=>$v) {
            if (strcmp('phone_ext',$k)==0 || strcmp('mobile_area_code',$k)==0 || strcmp('mobile_number',$k)==0) {
                continue;       
            }
            if ($sub_acc=='1') {
	            if (strcmp('group_name',$k)==0 || strcmp('street',$k)==0 || strcmp('city',$k)==0 || strcmp('state',$k)==0 
	                   || strcmp('country',$k)==0 || strcmp('zip',$k)==0) {
	                continue;       
	            }            	
            }            
            if (strlen($v)==0) {
                return array(
                    'success'=>false,
                    'errcode'=>'0002',        
                    'errmsg'=>$this->error->getErrorMessage('0002')                   
                );                              
            }               
            
        }
        
        $login_no = 0;
        
        //define role flag                
        $result_creater = $this->getUser($creater);
        if ($result_creater['success']===false) {
            return  $result_creater;
        }       
        $createrLogin = $result_creater['data'];                
        switch ($acc_type) {
        	case '1':
	        /* for dealer
	         * if isSubAcc=TRUE, 
	         *    creater = master dealer
	         *    group_no = creater
	         *    master_dealer = creater
	         * if isSubAcc=FALSE,
	         *    creater = admin
	         *    group_no = null (be created dealer login no later)
	         *    master_dealer = null (be created dealer login no later)
	         */        		
                $gno = (($sub_acc=='1')?$creater:null);
                $master_dealer = (($sub_acc=='1')?$creater:null);        		
                break;                
            case '2':
            /* for customer
             * if isSubAcc=TRUE, 
             *    creater = master customer
             *    group_no = creater
             *    master_dealer = creater's master dealer 
             * if isSubAcc=FALSE,
             *    creater = (master/sub) dealer
             *    group_no = null (be created customer login no later)
             *    master_dealer = creater's master dealer          
             */
                $gno = (($sub_acc=='1')?$creater:null);
                $master_dealer = &$createrLogin['master_dealer'];            	
                break;
            case '3':
            /* for account mgr
             * creater = (master/sub) customer
             * group_no = master cutomer
             * master_dealer = creater's master dealer 
             */
                $gno = &$createrLogin['group_no'];
                $master_dealer = &$createrLogin['master_dealer'];                
                break;                
            default:
                $gno = 0;
                $master_dealer = 0;
                break;
        }
        
        #1#create basic login info        
        $insertLoginSql = sprintf("INSERT INTO login SET login_id = '%s', password = '%s', first_name = '%s', last_name = '%s', phone_area_code = '%s' , phone = '%s', phone_ext = '%s', mobile_area_code = '%s', mobile_number = '%s', email = '%s', acc_type= '%d', creater =  %d, sub_acc = '%d' ",
                          $param['login_id'], md5($param['password']), $param['first_name'], $param['last_name'], $param['phone_area_code'], $param['phone'], $param['phone_ext'], $param['mobile_area_code'], $param['mobile_number'], $param['email'], $acc_type, $creater, $sub_acc);        
        if ($this->db->executeSQL($insertLoginSql)===false) {
        	
            return array(
                'success'=>false,
                'errcode'=> $this->isDuplicate()?'0006':'0003',        
                'errmsg'=>$this->isDuplicate()?$this->error->getErrorMessage('0006'):$this->error->getErrorMessage('0003')                 
            );   
        }
        if ($this->db->getConnection()->affected_rows==0) {
            return array(
                'success'=>false,
                'errcode'=>'1013',        
                'errmsg'=>$this->error->getErrorMessage('1013')                   
            );   
        }          
        //reset login no      
        $login_no = $this->db->getConnection()->insert_id;
        
        #2#update role flag        
        $gno = is_null($gno)?$login_no:$gno;
        $master_dealer = is_null($master_dealer)?$login_no:$master_dealer;
        $updLoginSql = sprintf("UPDATE login SET group_no = %d, master_dealer = %d WHERE login_no = %d ", $gno, $master_dealer, $login_no);        
        if ($this->db->executeSQL($updLoginSql)===false) {
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')                 
            );   
        }
        if ($this->db->getConnection()->affected_rows==0) {
        	logger('_addUser', '[updUserRoleInfoFail]SQL:'.$updLoginSql);
            return array(
                'success'=>false,
                'errcode'=>'1013',        
                'errmsg'=>$this->error->getErrorMessage('1013')                   
            );   
        }        
        
        #3#create group(biz) info if is master account and not account manager
        if ($sub_acc=='0' && $acc_type != '3') {        	
	        $groupParam = array(
	            "group_no"=> $login_no,
	            "group_name"=> &$param['group_name'],
	            "street"=> &$param['street'],
	            "city"=> &$param['city'],
	            "state"=>  &$param['state'],
	            "country"=> &$param['country'],
	            "zip"=> &$param['zip']
	        );      
	        $result_group = $this->_addGroup($groupParam);
	        if ($result_group['success']===false) {
	            return  $result_group;
	        }	        	
        }
                
        return array(
            'success'=>true,
            'errcode'=>0,    
            'data'=>$login_no
        );  
    }

    /*===========================================================================
     * Function Name : _addGroup
     * Description : add group(biz) info related to user (master customer/dealer only)
     * Input :
     *     $param = array(
     *          "group_no"=> user's group no 
     *          "group_name"=> user's company name
     *          "street"=> user's company location
     *          "city"=> user's company location
     *          "state"=>  user's company location
     *          "country"=>  user's company location
     *          "zip"=>  user's company location zip code
     *      );
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error
     * Side effects : none
     *=========================================================================*/           
    private function _addGroup($param){
        
        foreach($param as $k=>$v) {
            if (strlen($v)==0) {
                return array(
                    'success'=>false,
                    'errcode'=>'0002',        
                    'errmsg'=>$this->error->getErrorMessage('0002')                   
                );                              
            }
        }

        $insertBizGroupSql = sprintf("INSERT INTO biz_group SET group_no = '%d', group_name = '%s' , street = '%s', city = '%s', state = '%s' , country = '%s' , zip = '%s' ", 
                           $param['group_no'], $param['group_name'], $param['street'], $param['city'], $param['state'], $param['country'], $param['zip']);                          
        if ($this->db->executeSQL($insertBizGroupSql)===false) {
            return array(
                'success'=>false,
                'errcode'=> $this->isDuplicate()?'0006':'0003',        
                'errmsg'=>$this->isDuplicate()?$this->error->getErrorMessage('0006'):$this->error->getErrorMessage('0003')                 
            );   
        }
        if ($this->db->getConnection()->affected_rows==0) {
            return array(
                'success'=>false,
                'errcode'=>'1014',        
                'errmsg'=>$this->error->getErrorMessage('1014')                   
            );   
        }        
        
        return array(
            'success'=>true,
            'errcode'=>0,    
        );  
    }

    /*===========================================================================
     * Function Name : _updateUser
     * Description : update user's login profile
     * Input : 
     *     $login_no => user's login no
     *     $param = array(
     *          "first_name"=> user's first name
     *          "last_name"=> user's last name
     *          "phone_area_code"=>  user's phone
     *          "phone"=>  user's phone
     *          "phone_ext"=>  user's phone
     *          "mobile_area_code"=>  user's mobile
     *          "mobile_number"=>  user's mobile
     *          "email"=> user's email
     *      );
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     * Side effects : none
     *=========================================================================*/                       
    private function _updateUser($login_no, $param){

        if (strlen($login_no)==0 ) {
                return array(
                    'success'=>false,
                    'errcode'=>'0002',        
                    'errmsg'=>$this->error->getErrorMessage('0002')                   
                );                              
        }
        
        foreach($param as $k=>$v) {
            if (strcmp('phone_ext',$k)==0 || strcmp('mobile_area_code',$k)==0 || strcmp('mobile_number',$k)==0) {
                continue;       
            }
            if (strlen($v)==0) {
                return array(
                    'success'=>false,
                    'errcode'=>'0002',        
                    'errmsg'=>$this->error->getErrorMessage('0002')                   
                );                              
            }
        }
                
        $updateLoginSql = sprintf("UPDATE login SET first_name = '%s', last_name = '%s', phone_area_code = '%s' , phone = '%s', phone_ext = '%s', mobile_area_code = '%s', mobile_number = '%s', email = '%s' WHERE login_no = '%s' ",
                          $param['first_name'], $param['last_name'], $param['phone_area_code'], $param['phone'], $param['phone_ext'], $param['mobile_area_code'], $param['mobile_number'], $param['email'], $login_no);
        if ($this->db->executeSQL($updateLoginSql)===false) {
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')                   
            );   
        }
        /*
        if ($this->db->getConnection()->affected_rows==0) {
            return array(
                'success'=>false,
                'errcode'=>'1016',        
                'errmsg'=>$this->error->getErrorMessage('1016')                   
            );   
        }        
        */             
        return array(
            'success'=>true,
            'errcode'=>0,        
        );              
    }
    
    /*===========================================================================
     * Function Name : _updateGroup
     * Description : update group(biz) info related to user (master customer/dealer only)
     * Input :
     *     $param = array(
     *          "group_no"=> user's group no 
     *          "group_name"=> user's company name
     *          "street"=> user's company location
     *          "city"=> user's company location
     *          "state"=>  user's company location
     *          "country"=>  user's company location
     *          "zip"=>  user's company location zip code
     *      );
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error
     * Side effects : none
     *=========================================================================*/           
    private function _updateGroup($param){
        
        foreach($param as $k=>$v) {
            if (strlen($v)==0) {
                return array(
                    'success'=>false,
                    'errcode'=>'0002',        
                    'errmsg'=>$this->error->getErrorMessage('0002')                   
                );                              
            }
        }

        $updateBizGroupSql = sprintf("UPDATE biz_group SET group_name = '%s' , street = '%s', city = '%s', state = '%s' , country = '%s' , zip = '%s' WHERE group_no = '%s' ", 
                                $param['group_name'], $param['street'], $param['city'], $param['state'], $param['country'], $param['zip'], $param['group_no']);
        if ($this->db->executeSQL($updateBizGroupSql)===false) {
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')                 
            );   
        }
        /*
        if ($this->db->getConnection()->affected_rows==0) {
            return array(
                'success'=>false,
                'errcode'=>'1015',        
                'errmsg'=>$this->error->getErrorMessage('1015')                   
            );   
        }        
        */
        return array(
            'success'=>true,
            'errcode'=>0,    
        );  
    }
        
    /*===========================================================================
     * Function Name : _delUser
     * Description : delete login user
     * Input : 
     *     $login_no => user's login no
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     * Side effects : none
     *=========================================================================*/                       
    private function _delUser($login_no){

        if (empty($login_no)) {
            return array(
                'success'=>false,
                'errcode'=>'0002',        
                'errmsg'=>$this->error->getErrorMessage('0002')                   
            );              
        }
        
        $cmd = sprintf("DELETE FROM login WHERE login_no=%d", $login_no);
        if ($this->db->executeSQL($cmd)===false) {
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')                   
            );   
        }

        if ($this->db->getConnection()->affected_rows==0) {
            return array(
                'success'=>false,
                'errcode'=>'1017',        
                'errmsg'=>$this->error->getErrorMessage('1017')                   
            );   
        }

        return array(
            'success'=>true,
            'errcode'=>0,        
        );          
    
    }
        
    /*===========================================================================
     * Function Name : getUser
     * Description :  get login user info by login no
     * Input : 
     *     $login_no   => user login no          
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     *                           1001: id not exists
     *     $result['data'] => if success=true, return user data array( index key = db table field )
     * Side effects : none
     *=========================================================================*/               
    public function getUser($login_no){
    
        if (strlen($login_no)==0 ) {
            return array(
                'success'=>false,
                'errcode'=>'0002',        
                'errmsg'=>$this->error->getErrorMessage('0002')                   
            );              
        }       
        
        $select_matrix = '*';
        $table_matrix = 'login';
        $rule_matrix  = "login_no='{$login_no}'";
        $cmd = $this->getSelectSql($select_matrix,$table_matrix,$rule_matrix,null,null);
        
        if (($result=$this->db->executeSQL($cmd))===false) {            
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')                   
            );          
        }
        
        $affected = $result->num_rows;
        $row_login = $result->fetch_assoc();
        $this->db->freeResultSet($result);
        
        if ($affected==0) {
            return array(
                'success'=>false,
                'errcode'=>'1001',        
                'errmsg'=>$this->error->getErrorMessage('1001')                   
            );  
        }
        
        return array(
            'success'=>true,
            'errcode'=>0, 
            'data'=>$row_login,       
        );      
    }
        
    /*===========================================================================
     * Function Name : addDealer
     * Description : add user as dealer role
     * Input : 
     *     $creater => creater's login no
     *     $isSubAcc => set TRUE if this created user is a sub account , else set FALSE 
     *     $param => array(
     *          "login_id"=> user's login id
     *          "password"=> user's login password
     *          "first_name"=> user's first name
     *          "last_name"=> user's last name
     *          "phone_area_code"=>  user's phone
     *          "phone"=>  user's phone
     *          "phone_ext"=>  user's phone
     *          "mobile_area_code"=>  user's mobile
     *          "mobile_number"=>  user's mobile
     *          "email"=> user's email
     *          "group_name"=> user's company name, provide if is a master user
     *          "street"=> user's company location, provide if is a master user
     *          "city"=> user's company location, provide if is a master user
     *          "state"=>  user's company location, provide if is a master user
     *          "country"=>  user's company location, provide if is a master user
     *          "zip"=>  user's company location zip code, provide if is a master user
     *      );
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     * Side effects : none
     *=========================================================================*/           
    public function addDealer($creater, $isSubAcc=false, $param){
        return $this->_addUser($creater, '1', (($isSubAcc===true)?'1':'0'), $param);        
    }
    
    /*===========================================================================
     * Function Name : addCustomer
     * Description : add user as customer role
     * Input : 
     *     $creater => creater's login no
     *     $isSubAcc => set TRUE if this created user is a sub account , else set FALSE 
     *     $param = array(
     *          "login_id"=> user's login id
     *          "password"=> user's login password
     *          "first_name"=> user's first name
     *          "last_name"=> user's last name
     *          "phone_area_code"=>  user's phone
     *          "phone"=>  user's phone
     *          "phone_ext"=>  user's phone
     *          "mobile_area_code"=>  user's mobile
     *          "mobile_number"=>  user's mobile
     *          "email"=> user's email
     *          "group_name"=> user's company name, provide if is a master user
     *          "street"=> user's company location, provide if is a master user
     *          "city"=> user's company location, provide if is a master user
     *          "state"=>  user's company location, provide if is a master user
     *          "country"=>  user's company location, provide if is a master user
     *          "zip"=>  user's company location zip code, provide if is a master user
     *      );
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     * Side effects : none
     *=========================================================================*/           
    public function addCustomer($creater, $isSubAcc=false, $param){    	
        return $this->_addUser($creater, '2', (($isSubAcc)?'1':'0'), $param);            	
    }

    /*===========================================================================
     * Function Name : addAccountManager
     * Description : add user as account manager which is belong to customer role 
     * Input : 
     *     $creater => creater's login no
     *     $param = array(
     *          "login_id"=> user's login id
     *          "password"=> user's login password
     *          "first_name"=> user's first name
     *          "last_name"=> user's last name
     *          "phone_area_code"=>  user's phone
     *          "phone"=>  user's phone
     *          "phone_ext"=>  user's phone
     *          "mobile_area_code"=>  user's mobile
     *          "mobile_number"=>  user's mobile
     *          "email"=> user's email
     *          "group_name"=> user's company name
     *          "street"=> user's company location
     *          "city"=> user's company location
     *          "state"=>  user's company location
     *          "country"=>  user's company location
     *          "zip"=>  user's company location zip code
     *      );
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     * Side effects : none
     *=========================================================================*/           
    public function addAccountManager($creater, $param){        
        return $this->_addUser($creater, '3', '0', $param);
    }
    
    /*===========================================================================
     * Function Name : updateProfile
     * Description : update user's login&group info
     * Input : 
     *     $login_no => user's login no
     *     $param => array(
     *          "first_name"=> user's first name
     *          "last_name"=> user's last name
     *          "phone_area_code"=>  user's phone
     *          "phone"=>  user's phone
     *          "phone_ext"=>  user's phone
     *          "mobile_area_code"=>  user's mobile
     *          "mobile_number"=>  user's mobile
     *          "email"=> user's email
     *          "group_name"=> user's company name, provide if is a master user
     *          "street"=> user's company location, provide if is a master user
     *          "city"=> user's company location, provide if is a master user
     *          "state"=>  user's company location, provide if is a master user
     *          "country"=>  user's company location, provide if is a master user
     *          "zip"=>  user's company location zip code, provide if is a master user
     *      );
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     * Side effects : none
     *=========================================================================*/           
    public function updateProfile($login_no, $param){
    	
        #1# get user profile & validate params
        if (strlen($login_no)==0) {
                return array(
                    'success'=>false,
                    'errcode'=>'0002',        
                    'errmsg'=>$this->error->getErrorMessage('0002')                   
                );                              
        }       
        $result_user = $this->getUser($login_no);
        if ($result_user['success']===false) {
            return $result_user;
        }                
        $acc_type = &$result_user['data']['acc_type'];
        $sub_acc = &$result_user['data']['sub_acc'];
        $group_no = &$result_user['data']['group_no'];
        
        $userParam = array(
                "first_name"=>  &$param['first_name'],
                "last_name"=> &$param['last_name'],
                "phone_area_code"=> &$param['phone_area_code'],
                "phone"=> &$param['phone'],
                "phone_ext"=>  &$param['phone_ext'],
                "mobile_area_code"=> &$param['mobile_area_code'],
                "mobile_number"=> &$param['mobile_number'],
                "email"=> &$param['email']
        );      
        $result_upduser = $this->_updateUser($login_no, $userParam);
        if ($result_upduser['success']===false) {
            return  $result_upduser;
        }               

        #3#create group(biz) info if is master account and not account manager
        if ($sub_acc=='0' && $acc_type != '3') {            
            $groupParam = array(
                "group_no"=> &$group_no,
                "group_name"=> &$param['group_name'],
                "street"=> &$param['street'],
                "city"=> &$param['city'],
                "state"=>  &$param['state'],
                "country"=> &$param['country'],
                "zip"=> &$param['zip']
            );      
            $result_group = $this->_updateGroup($groupParam);
            if ($result_group['success']===false) {
                return  $result_group;
            }               
        }        

        return array(
            'success'=>true,
            'errcode'=>0,    
        );          
    }
    
    /*===========================================================================
     * Function Name : getProfile
     * Description :  get user & biz group info by login no
     * Input : 
     *     $login_no        => user login no 
     *     $isSubAcc   => specified the login no as sub account or not        
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     *                           0006: user not exists
     *     $result['data'] => if success=true, return user data array( index key = db table field )
     * Side effects : none
     *=========================================================================*/               
    public function getProfile($login_no){
    	    
        if (empty($login_no)) {
            return array(
                'success'=>false,
                'errcode'=>'0002',        
                'errmsg'=>$this->error->getErrorMessage('0002')                   
            );              
        }
                          
        $cmd = sprintf("SELECT l.*, b.* FROM login as l, biz_group as b WHERE l.login_no=%d AND l.group_no=b.group_no LIMIT 1 ", $login_no );        
        if (($result=$this->db->executeSQL($cmd))===false) {            
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')                   
            );          
        }
        
        $affected = $result->num_rows;
        $row_login = $result->fetch_assoc();
        $this->db->freeResultSet($result);
        
        if ($affected==0) {
            return array(
                'success'=>false,
                'errcode'=>'1006',                   
                'errmsg'=>$this->error->getErrorMessage('1006')        
            );  
        }
        
        return array(
            'success'=>true,
            'errcode'=>0, 
            'data'=>$row_login,       
        );      
        
    }


    /*===========================================================================
     * Function Name : updateCustomer
     * Description : save user's profile
     * Input : 
     *     $param = array(
     *          "first_name"=> user's first name
     *          "last_name"=> user's last name
     *          "phone_area_code"=>  user's phone
     *          "phone"=>  user's phone
     *          "phone_ext"=>  user's phone
     *          "mobile_area_code"=>  user's mobile
     *          "mobile_number"=>  user's mobile
     *          "email"=> user's email
     *          "group_name"=>  user's company name
     *          "street"=> company address
     *          "city"=> company city
     *          "state"=> company state
     *          "zip"=> company zip code
     *          "cno"=> user's login no
     *          "gno"=> user's creater login no
     *      );
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     * Side effects : none
     *=========================================================================                       
    public function updateCustomer($param){

        foreach($param as $k=>$v) {
            if (strcmp('phone_ext',$k)==0 || strcmp('mobile_area_code',$k)==0 || strcmp('mobile_number',$k)==0) {
                continue;       
            }
                        
            if (strlen($v)==0) {
                return array(
                    'success'=>false,
                    'errcode'=>'0002',        
                    'errmsg'=>$this->error->getErrorMessage('0002')                   
                );                              
            }
        }
                
        $updateLoginSql = sprintf("UPDATE login SET first_name = '%s', last_name = '%s', phone_area_code = '%s' , phone = '%s', phone_ext = '%s', mobile_area_code = '%s', mobile_number = '%s', email = '%s' WHERE login_no = '%s' ",
                          $param['first_name'], $param['last_name'], $param['phone_area_code'], $param['phone'], $param['phone_ext'], $param['mobile_area_code'], $param['mobile_number'], $param['email'], $param['cno']);

        if ($this->db->executeSQL($updateLoginSql)===false) {
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')                   
            );   
        }

        $updateBizGroupSql = sprintf("UPDATE biz_group SET group_name = '%s' , street = '%s', city = '%s', state = '%s' , zip = '%s' WHERE group_no = '%s' ", 
                                $param['group_name'], $param['street'], $param['city'], $param['state'], $param['zip'], $param['gno']);

        if ($this->db->executeSQL($updateBizGroupSql)===false) {
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')                   
            );   
        } 
             
        return array(
            'success'=>true,
            'errcode'=>0,        
        );              
    }
    */

    /*===========================================================================
     * Function Name : delSubLoginUser
     * Description : delete user who shold be sub account or account manager
     * Input : 
     *     $login_no => user's login no
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     * Side effects : none
     *=========================================================================*/           
    public function delSubLoginUser($login_no){
        
        #1# get user profile & validate params
        if (strlen($login_no)==0) {
                return array(
                    'success'=>false,
                    'errcode'=>'0002',        
                    'errmsg'=>$this->error->getErrorMessage('0002')                   
                );                              
        }       
        $result_user = $this->getUser($login_no);
        if ($result_user['success']===false) {
            return $result_user;
        }                
        $acc_type = &$result_user['data']['acc_type'];
        $sub_acc = &$result_user['data']['sub_acc'];
        //$group_no = &$result_user['data']['group_no'];
        
        if ($acc_type != '3' && $sub_acc=='0') {  
                return array(
                    'success'=>false,
                    'errcode'=>'0002',        
                    'errmsg'=>$this->error->getErrorMessage('0002')                   
                );                              
        }        

        $result_del = $this->_delUser($login_no);
        if ($result_del['success']===false) {
            return  $result_upduser;
        }               
        
        return array(
            'success'=>true,
            'errcode'=>0,    
        );          
    }
            
    /*===========================================================================
     * Function Name : getSubLoginUserCount
     * Description : get total amount of user
     * Input : 
     *     $group_no   => master (customer/dealer) login no
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     *     $result['data'] => if success=true, return amount number
     * Side effects : none
     *=========================================================================*/       
    public function getSubLoginUserCount($group_no){
                
        if (empty($group_no)) {
            return array(
                'success'=>false,
                'errcode'=>'0002',        
                'errmsg'=>$this->error->getErrorMessage('0002')                   
            );              
        }
    	
        $getUserListSql = sprintf("SELECT COUNT(*) FROM login WHERE group_no=%d AND acc_type!='3' AND sub_acc='1' ", $group_no );
        if (($result = $this->db->executeSQL($getUserListSql)) === false) {            
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')
            );          
        }
        
        $row = $result->fetch_row();
        $total = $row[0]; 
        $this->db->freeResultSet($result);
                        
        return array(
            'success'=>true,
            'errcode'=>0, 
            'data'=>$total
        );                  
    }
    
    /*===========================================================================
     * Function Name : getSubLoginUserList
     * Description : get user list which
     * Input : 
     *     $group_no   => master (customer/dealer) login no
     *     $start      => db record start index
     *     $limit      => records per request , use '*' to request all records      
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     *     $result['recordset'] => return mysql result set
     *     $result['recordnum'] => return mysql query result count
     * Side effects : none
     *=========================================================================*/       
    public function getSubLoginUserList($group_no, $start = 0, $limit = 10){
                
        if (empty($group_no)) {
            return array(
                'success'=>false,
                'errcode'=>'0002',        
                'errmsg'=>$this->error->getErrorMessage('0002')                   
            );              
        }
    	        
        $limitSQL = ($limit != '*')?"LIMIT {$start}, {$limit}":"";                
        $getUserListSql = sprintf("SELECT * FROM login WHERE group_no=%d AND acc_type!='3' AND sub_acc='1' %s ", $group_no, $limitSQL );        
        if (($result = $this->db->executeSQL($getUserListSql)) === false) {            
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')
            );          
        }
        
        return array(
            'success'=>true,
            'errcode'=>0, 
            'recordset'=>$result,
            'recordnum'=>$result->num_rows
        );
    }
        
    /*===========================================================================
     * Function Name : getAccountManagerUserCount
     * Description : get total amount of account mgr 
     * Input : 
     *     $group_no   => customer's group no
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     *     $result['data'] => if success=true, return amount number
     * Side effects : none
     *=========================================================================*/       
    public function getAccountManagerUserCount($group_no){
                
        if (empty($group_no)) {
            return array(
                'success'=>false,
                'errcode'=>'0002',        
                'errmsg'=>$this->error->getErrorMessage('0002')                   
            );              
        }
    	
        $getUserListSql = sprintf("SELECT COUNT(*) FROM login WHERE group_no=%d AND acc_type='3' AND sub_acc='0' ", $group_no );                
        if (($result = $this->db->executeSQL($getUserListSql)) === false) {            
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')
            );          
        }
        
        $row = $result->fetch_row();
        $total = $row[0]; 
        $this->db->freeResultSet($result);
                        
        return array(
            'success'=>true,
            'errcode'=>0, 
            'data'=>$total
        );                  
    }
        
    /*===========================================================================
     * Function Name : getAccountManagerUserList
     * Description : get account mgr user list
     * Input : 
     *     $group_no   => customer's group no
     *     $start      => db record start index
     *     $limit      => records per request , use '*' to request all records      
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     *     $result['recordset'] => return mysql result set
     *     $result['recordnum'] => return mysql query result count
     * Side effects : none
     *=========================================================================*/       
    public function getAccountManagerUserList($group_no, $start = 0, $limit = 10){
        
        if (empty($group_no)) {
            return array(
                'success'=>false,
                'errcode'=>'0002',        
                'errmsg'=>$this->error->getErrorMessage('0002')                   
            );              
        }
    	
        $limitSQL = ($limit != '*')?"LIMIT {$start}, {$limit}":"";                
        $getUserListSql = sprintf("SELECT * FROM login WHERE group_no=%d AND acc_type='3' AND sub_acc='0' ORDER BY login_id %s ", $group_no, $limitSQL );         
        if (($result = $this->db->executeSQL($getUserListSql)) === false) {            
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')
            );          
        }
        
        return array(
            'success'=>true,
            'errcode'=>0, 
            'recordset'=>$result,
            'recordnum'=>$result->num_rows
        );            
    } 
        
    /*===========================================================================
     * Function Name : getCustomerProfileCount
     * Description : get total amount of master customer profile which might be based on master dealer or not
     * Input : 
     *     $dealer_no  => dealer's group no=>master_dealer
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     *     $result['recordset'] => return mysql result set
     *     $result['recordnum'] => return mysql query result count
     * Side effects : none
     *=========================================================================*/       
    public function getCustomerProfileCount($dealer_no){
                
        if (empty($dealer_no)) {
            return array(
                'success'=>false,
                'errcode'=>'0002',        
                'errmsg'=>$this->error->getErrorMessage('0002')                   
            );              
        }
        
        $extraSQL = isset($dealer_no)?"AND master_dealer={$dealer_no}":"";        
        $cmd = sprintf("SELECT COUNT(*) FROM login WHERE acc_type='2' AND sub_acc='0' %s ", $extraSQL );                
        if (($result = $this->db->executeSQL($cmd)) === false) {            
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')
            );          
        }
        
        $row = $result->fetch_row();
        $total = $row[0]; 
        $this->db->freeResultSet($result);
                        
        return array(
            'success'=>true,
            'errcode'=>0, 
            'data'=>$total
        );                  
    } 
    
    /*===========================================================================
     * Function Name : getCustomerProfileList
     * Description : get master customer profile list which might be based on master dealer or not
     * Input : 
     *     $dealer_no  => dealer's group no=>master_dealer
     *     $start      => db record start index
     *     $limit      => records per request , use '*' to request all records      
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     *     $result['recordset'] => return mysql result set
     *     $result['recordnum'] => return mysql query result count
     * Side effects : none
     *=========================================================================*/       
    public function getCustomerProfileList($dealer_no=null, $start = 0, $limit = 10){
    	
        $extraSQL = isset($dealer_no)?"AND l.master_dealer={$dealer_no}":"";        
        $limitSQL = ($limit != '*')?"LIMIT {$start}, {$limit}":"";                
        $cmd = sprintf("SELECT l.*, b.* FROM login l, biz_group b WHERE l.group_no=b.group_no AND l.acc_type='2' AND l.sub_acc='0' %s ORDER BY l.login_id %s ", 
                        $extraSQL, $limitSQL );         
        
        if (($result = $this->db->executeSQL($cmd)) === false) {            
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')
            );          
        }
        
        return array(
            'success'=>true,
            'errcode'=>0, 
            'recordset'=>$result,
            'recordnum'=>$result->num_rows
        );    	
    }
   
    /*===========================================================================
     * Function Name : getDealerProfileCount
     * Description : get total amount of master dealer profile
     * Input : 
     *     none   
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     *     $result['recordset'] => return mysql result set
     *     $result['recordnum'] => return mysql query result count
     * Side effects : none
     *=========================================================================*/       
    public function getDealerProfileCount(){
                        
        $cmd = "SELECT COUNT(*) FROM login WHERE acc_type='1' AND sub_acc='0'";                
        if (($result = $this->db->executeSQL($cmd)) === false) {            
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')
            );          
        }
        
        $row = $result->fetch_row();
        $total = $row[0]; 
        $this->db->freeResultSet($result);
                        
        return array(
            'success'=>true,
            'errcode'=>0, 
            'data'=>$total
        );                  
    } 
        
    /*===========================================================================
     * Function Name : getDealerProfileList
     * Description : get master dealer profile list
     * Input : 
     *     $start      => db record start index
     *     $limit      => records per request , use '*' to request all records      
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     *     $result['recordset'] => return mysql result set
     *     $result['recordnum'] => return mysql query result count
     * Side effects : none
     *=========================================================================*/       
    public function getDealerProfileList($start = 0, $limit = 10){
        
        $cmd = "SELECT l.*, b.* FROM login l, biz_group b WHERE l.group_no=b.group_no AND l.acc_type='1' AND l.sub_acc='0' ORDER BY l.login_id ";
    	if (($result = $this->db->executeSQL($cmd)) === false) {            
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')
            );          
        }
        
        return array(
            'success'=>true,
            'errcode'=>0, 
            'recordset'=>$result,
            'recordnum'=>$result->num_rows
        );            
    }    
	
    /*===========================================================================
     * Function Name : searchCustomer
     * Description : search customer list in specified key
     * Input : 
     *     $dealer_no  => dealer no = login no
     *     $kwd        => search key word
     *     $searchType => target table field    
     *     $start      => db record start index
     *     $limit      => records per request , use '*' to request all records      
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     *     $result['recordset'] => return mysql result set
     *     $result['recordnum'] => return mysql query result count
     * Side effects : none
     *=========================================================================*/                           
	public function searchCustomer($dealer_no, $kwd, $searchType, $start = 0, $limit = 10){
	
		$getSearchCustSql = "SELECT l.*, b.* FROM login l, biz_group b ";
		$getSearchCustSql .= "WHERE b.{$searchType} LIKE '%{$kwd}%' AND l.login_no = b.group_no ";
		$getSearchCustSql .= isset($creater)?"AND l.creater = '{$dealer_no}'":'';
		$getSearchCustSql .= ($limit != '*')?"LIMIT {$start}, {$limit}":"";
		
		if(($result = $this->db->executeSQL($getSearchCustSql))===false){
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')
            );          					
		}
		
        /* same return format with SLTUnit->searchUnit */
        return array(
            'success'=>true,
            'errcode'=>0, 
            'recordset'=>$result,
            'recordnum'=>$result->num_rows
        );        
	}

        	
    /*===========================================================================
     * Function Name : updateCustomerLogo
     * Description : save user's biz logo info
     * Input : 
     *     $gno        => master customer's login no 
     *     $file       => $_FILE data (name, type, size, tmp_name, error)     
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     * Side effects : none
     *=========================================================================*/               	
    public function updateCustomerLogo($gno, $file){

        if (empty($gno)) {
            return array(
                'success'=>false,
                'errcode'=>'0002',        
                'errmsg'=>$this->error->getErrorMessage('0002')                   
            );              
        }
         
        if (empty($file) || $file['error'] || $file['size']==0) {
            return array(
                'success'=>false,
                'errcode'=>'1009',        
                'errmsg'=>$this->error->getErrorMessage('1009')                   
            );                  	
        }
		
        if (!file_exists($file['tmp_name']) || !is_file($file['tmp_name'])) {
            return array(
                'success'=>false,
                'errcode'=>'1007',        
                'errmsg'=>$this->error->getErrorMessage('1007')                   
            );        	
        }

        if (($logo_size = filesize($file['tmp_name']))==0) {
            return array(
                'success'=>false,
                'errcode'=>'1009',        
                'errmsg'=>$this->error->getErrorMessage('1009')                   
            );           	
        }
        //read file content and then del it
        $logo_file = addslashes(file_get_contents($file['tmp_name']));  
        unlink($file['tmp_name']);
        
        $cmd = sprintf("UPDATE biz_group SET logo_filepath='%s', logo_name='%s', logo_size=%d, logo_type='%s', logo='%s'  WHERE group_no=%d ", 
                        '', $file['name'], $logo_size, $file['type'], $logo_file, $gno);               
        if (($this->db->executeSQL($cmd))===false) {
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')                   
            );          
        }        
    
        if ($this->db->getConnection()->affected_rows==0) {
            return array(
                'success'=>false,
                'errcode'=>'0004',        
                'errmsg'=>$this->error->getErrorMessage('0004')                   
            );   
        }
        
        return array(
            'success'=>true,
            'errcode'=>0
        );          	
    }

    /*===========================================================================
     * Function Name : getCustomerLogo
     * Description : get user's biz logo info
     * Input : 
     *     $gno        => master customer's login no 
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     *     $result['data'] =>if success=true, return user data array( index key = db table field )
     * Side effects : none
     *=========================================================================*/                   
    public function getCustomerLogo($gno){
            
        if (empty($gno)) {
            return array(
                'success'=>false,
                'errcode'=>'0002',        
                'errmsg'=>$this->error->getErrorMessage('0002')                   
            );              
        }
        
        $cmd = sprintf("SELECT logo_type, logo FROM biz_group WHERE group_no=%d ", $gno);               
        if (($result=$this->db->executeSQL($cmd))===false) {            
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')                   
            );          
        }    

        $affected = $result->num_rows;        
        $row = $result->fetch_assoc(); 
        $this->db->freeResultSet($result);        
        
        if ($affected==0) {
            return array(
                'success'=>false,
                'errcode'=>'1011',        
                'errmsg'=>$this->error->getErrorMessage('1011')                   
            );  
        }   
             
        return array(
            'success'=>true,
            'errcode'=>0, 
            'data'=> $row       
        );  
        
    }

    /*===========================================================================
     * Function Name : checkLoginAccountExist
     * Description : check loginId exist 
     * Input : 
     *     $loginId => login Id
     * Output : 
     *     none   
     * Return : 
     *     $result['success'] => query result, true or false
     *     $result['errcode'] => 0: success, 0002: parameter not matched, 0003: db error, 
     *     $result['data'] => if exist = true, not exist = false
     * Side effects : none
     *=========================================================================*/ 	
	public function checkLoginAccountExist($loginId){
	
        if (empty($loginId)) {
            return array(
                'success'=>false,
                'errcode'=>'0002',        
                'errmsg'=>$this->error->getErrorMessage('0002')                   
            );              
        }
        
        $cmd = sprintf("SELECT COUNT(*) AS num FROM login WHERE login_id='%s' ", $loginId);    
		
        if (($result=$this->db->executeSQL($cmd))===false) {            
            return array(
                'success'=>false,
                'errcode'=>'0003',        
                'errmsg'=>$this->error->getErrorMessage('0003')                   
            );          
        }    
     
        $row = $result->fetch_assoc();
		$existFlag = ($row['num'] > 0)?true:false;
        $this->db->freeResultSet($result); 

		return array(
			'success' => true,
			'errcode' => 0,        
			'data' => $existFlag                
		);  
		
	}	
}

?>