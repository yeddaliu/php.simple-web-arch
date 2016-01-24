<?php
if (!defined('TRACE_LOG_ROOT')) {
    define('TRACE_LOG_ROOT','/var/log');    
}
if (!defined('SERVICE_ROOT')) {
    defined('SERVICE_ROOT', dirname(__FILE__).'../services');
}

require_once(SERVICE_ROOT.'/class.iDBConnectionFactory.php');
require_once(SERVICE_ROOT.'/class.errorCodeHandler.php');

class DBBaseModel{
	protected $db;
	protected $error;
	protected $dblog;
	
	//public function DBBaseModel(){
	public function __construct() {
		$this->db = iDBConnectionFactory::getInstance();
		$this->error = ErrorCodeHandler::getInstance();
		
		$base = TRACE_LOG_ROOT;
		if (!defined('TRACE_LOG_ROOT') || !is_writable(TRACE_LOG_ROOT)) {
			$base = '/var/log/';
		}		
		$this->dblog = TRACE_LOG_ROOT.'/dbmodel_'.Date("Y").'.log';
	}
	
	/* no need to free connections from iDBConnectionFactory
	public function destroy() {		
		$this->db->freeConnection();			
	}
    */
    public function isDuplicate() {
       return ($this->db->getConnection()->errno=='1062')?true:false;
    }	
	
    /*
     * log
     */
	public function logger($method_name='', $msg) {
		if (empty($msg)) return false;	
		$logger_msg = date("Y-m-d H:i:s").' '.(empty($method_name)?'['.get_class($this).']':"[{$method_name}]").' '.$msg."\n";	
		error_log($logger_msg, 3, $this->dblog);
		return true;
	}
	
	/*
	 * fast sql generater
	 */
    public function getSelectSql($select_matrix, $table_matrix, $rule_matrix, $order_matrix, $limit_matrix) {
        $select_elements = $this->matrix2string($select_matrix,0);
        $from_sql = $this->getFromSql($table_matrix);
        $where_sql = $this->getWhereSql($rule_matrix,0);
        $order_sql = $this->getOrderSql($order_matrix);
        $limit_sql = $this->getLimitSql($limit_matrix);
        return "select {$select_elements} {$from_sql} {$where_sql} {$order_sql} {$limit_sql}";
    }
    
    public function getJoinSelectSql($select_matrix,$table_matrix,$rule_matrix,$join_rule_matrix,$order_matrix,$limit_matrix){
        $select_elements = $this->matrix2string($select_matrix,0);
        $from_sql = $this->getFromSql($table_matrix);
        $where_sql = $this->getWhereSql($rule_matrix,0);
        $join_sql =  (preg_match('/where/',$where_sql))
            ?' and '.str_replace('where','',$this->getWhereSql($join_rule_matrix,1))
            :$this->getWhereSql($join_rule_matrix,1);
        $order_sql = $this->getOrderSql($order_matrix);
        $limit_sql = $this->getLimitSql($limit_matrix);
        return "select {$select_elements} {$from_sql} {$where_sql} {$join_sql} {$order_sql} {$limit_sql}";
    }
    
    public function getInsertSql($table,$matrix){
        return $this->getWritableSql($table,$matrix,'insert');
    }
    
    public function getUpdateSql($table,$matrix,$rule_matrix){
        return $this->getWritableSql($table,$matrix,'update').$this->getWhereSql($rule_matrix,0);
    }
    
    public function getDeleteSql($table,$rule_matrix){
        return "delete from {$table} ".$this->getWhereSql($rule_matrix,0);
    }
	
    /*
     * private methods
     */
    private function matrix2string($matrix,$quoted=0){
        $matrix2string = '';
        if(!is_array($matrix)) return $matrix;
        
        foreach($matrix as $k=>$v){
            if(is_numeric($k)) return implode(',',$matrix);
            $matrix2string .= ($quoted===1)?"{$k}={$v},":"{$k}='{$v}',";
        }
        return substr($matrix2string,0,-1);
    }
    
    private function getFromSql($table_matrix){
        $table_elements = (is_array($table_matrix))
            ?$this->matrix2string($table_matrix, 0)
            :$table_matrix;
        if(strlen($table_elements)<1) return;
        return 'from '.$table_elements;
    }    
    
    private function getWhereSql($where_matrix,$quoted=0){
        $string = $this->matrix2string($where_matrix,$quoted);
        $string = str_replace(',',' and ',$string);
        
        if(empty($string)) return '';
        else return ' where '.$string;
    }    
        
    private function getOrderSql($order_matrix){
        if(!is_array($order_matrix)) return $order_matrix;
        
        $order_string = '';
        if(sizeof($order_matrix)>0){
            foreach($order_matrix as $k => $v){
                $order_string .= $k.' '.$v.',';
            } 
            $order_string = substr($order_string,0,-1);
        }
        return 'order by '.$order_string;
    }
    
    private function getLimitSql($limit_matrix){
        if(!is_array($limit_matrix)) return $limit_matrix;        
        $limit_elements = $this->matrix2string($limit_matrix,0);
        if(strlen($limit_elements)<1) return;
        return 'limit '.$limit_elements;
    }
        
    private function getWritableSql($table,$matrix,$type){
        if($type === 'insert') $act = "insert into";
        if($type === 'update') $act = "update";
        $string = $this->matrix2string($matrix,0);
        return $sql = "{$act} {$table} set {$string}";
    }    
}

?>