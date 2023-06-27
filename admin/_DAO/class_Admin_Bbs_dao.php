<?php

class Admin_Bbs_DAO extends Database {

	public function __construct($select_db_name) {
            parent::__construct($select_db_name);
	}
        
        public function __destruct() {
            parent::__destruct();
	}

	public function dbconnect(){
		return $this->connect();
	}

	public function dbclose(){
		return $this->disconnect();
	}

	public function dbfree(){
		return $this->free();
	}

	public function ArrayCount($array){
		if(isset($array)) return count($array);
		return -1;
	}

	public function getQueryData($g_data){
		$sql = $g_data['sql'];
	
		//$sql_str = "[getQueryData] [".$sql."] ";
		//CommonUtil::logWrite($sql_str,"pop_memo");
		
		//echo $sql ."<br>";
		$recordset = $this->select_query($sql);
	
		return $recordset;
	}
	
	public function getTotalCount($g_data){
		$sql  = "SELECT COUNT(*) AS CNT FROM ".$g_data['table_name']." ";
		$sql .= $g_data["sql_where"].";";
	
		//echo $sql ."<br>";
	
		$recordset = $this->select_query($sql);
	
		return $recordset;
	}

	public function setQueryData($g_data){
	    $sql = $g_data['sql'];
	    
	    //echo $sql ."<br>";
	    $recordset = $this->execute_query($sql);
	    
	    return $recordset;
	}
	
	
}
	
?>
		