<?php
if (!defined('GLOBAL_DEF_ROOT')) {
    defined('GLOBAL_DEF_ROOT', dirname(__FILE__).'../global_define');
}

class iDBConnectionFactory{
	
	private static $instance;
	
	private $host; 
	private $port;
	private $database;
	private $user;
	private $password;
	private $connection;
	private $dbconfig;
	private $transaction_in_progress;

	public static function getInstance() {
        if (!isset(self::$instance)) {
            $classname = __CLASS__;
            self::$instance = new $classname;
        }

        return self::$instance;
    }    
    	
    private function __construct() {
    	include_once(GLOBAL_DEF_ROOT.'/db.inc');
    	
        $this->user = DB_USER;
        $this->password = DB_PWD;
        $this->host = DB_HOST;
        $this->port = DB_PORT;
        $this->database = DB_NAME;
        $this->transaction_in_progress = false;
        
        $this->connection = null;
        $this->connect();
    }

    public function __clone() {
        trigger_error('Clone iDBConnectionFactory is not allowed.', E_USER_ERROR);
    }
        
    /* It is not necessary to destroy iDBConnectionFactory,
     * cause we use singletons pattern to handle this object
    public function destroy(){
        $this->freeConnection();
        settype($this, 'null');     
    }
    */
        
	public function connect(){
        //must check in case connection has been closed
		if($this->connection!=null){
                return TRUE;
		}
		
		$this->connection = new mysqli($this->host, $this->user, $this->password, $this->database, $this->port);
		if (mysqli_connect_errno()) {
			$this->connection = null;
			return FALSE;
		}
			
		if (!$this->connection->set_charset("utf8")) {
			$this->connection = null;
    		return FALSE;
    	}

		return TRUE;
	}
	
	public function getConnection(){
		return $this->connection;
	}
	
    public function isConnected(){
        return (!is_null($this->connection));   
    }

    /*
     * Note: Using mysql_close() isn't usually necessary, 
     * as non-persistent open links are automatically closed at the end of the script's execution.
     */
	public function freeConnection(){
		if($this->isConnected()){
			$this->connection->close();
			settype($this->connection, 'null');
		}
		return TRUE;		
	}
	
	public function executeSQL($sqlCmd){
		if ($this->connect()===false) {
			return FALSE;
		}
		
		$result = $this->connection->query($sqlCmd);
		if ($result===false) {
			return FALSE;
		}
		return $result;							
	}
	
	public function freeResultSet($resultSet){
		mysqli_free_result($resultSet);
		settype($resultSet, 'null');
		return TRUE;
	}
 	
	public function getDBName() {
		return $this->database;
	}

    public function selectDB($dbname=null){
        if (empty($dbname)) {
            return;
        }    	
        $this->database = $dbname;
        return $this->connection->select_db($this->database);
    }        	
}	
?>
