<?php

class Admin_Member_DAO extends Database {

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
	
	public function getTotalCount($g_data){
		$sql  = "SELECT COUNT(*) AS CNT FROM ".$g_data['table_name']." ";
		$sql .= $g_data["sql_where"].";";
	
		//echo $sql ."<br>";
	        CommonUtil::logWrite("getTotalCount 2: " . $sql, "info");
		$recordset = $this->select_query($sql);
	
		return $recordset;
	}
	
	
	public function getUserList($g_data){
	    $start 	= ( $g_data['page'] -1 ) * $g_data['num_per_page'];
	    
	    $sql  = "SELECT a.idx, a.id, a.nick_name, a.u_business, a.money, a.point, a.betting_p, a.is_recommend, a.call ";
	    $sql .= ", a.status, a.level, a.auto_level, a.last_login, a.MICRO, a.AG, a.recommend_code, a.recommend_member ";
	    $sql .= ", a.account_number, a.account_name, a.account_bank, a.is_monitor, a.is_monitor_charge, a.is_monitor_security ";
	    $sql .= ", a.is_monitor_bet, a.dis_id, a.dis_line_id, a.reg_time ";
	    $sql .= ", (SELECT b.id FROM member b WHERE b.idx=a.recommend_member) AS re_id ";
	    $sql .= " FROM member a ";
	    
	    $sql .= $g_data["sql_where"];
	    $sql .= $g_data["sql_orderby"];
	    
	    $sql .= " LIMIT $start, ".$g_data['num_per_page'].";";
	    
	    //$sql_str = "[getUserList] [".$sql."] ";
	    //CommonUtil::logWrite($sql_str,"debug");
	    
	    $recordset = $this->select_query($sql);
	    
	    return $recordset;
	}
	
	public function getMsgListCount($g_data){
	    $sql = "SELECT COUNT(*) AS CNT ";
	    $sql .= " FROM t_message a, member b ";
	    $sql .= $g_data["sql_where"];

	    //$sql_str = "[getUserList] [".$sql."] ";
	    //CommonUtil::logWrite($sql_str,"pop_memo");
	    
	    $recordset = $this->select_query($sql);
	    
	    return $recordset;
	}
	
	
	public function getMsgList($g_data){
	    $start 	= ( $g_data['page'] -1 ) * $g_data['num_per_page'];
	    
	    $sql = "SELECT a.idx, a.msg_idx, a.member_idx, a.read_yn ";
	    $sql .= " , a.u_ip, a.reg_time, a.read_time ";
	    $sql .= " , b.idx as m_idx, b.id, b.nick_name, b.level, b.dis_id, b.recommend_member, b.status ";
	    $sql .= " , c.title, c.content ";
	    $sql .= " FROM t_message a ";
	    $sql .= " LEFT OUTER JOIN member b ON a.member_idx = b.idx ";
	    $sql .= " LEFT OUTER JOIN t_message_list c ON a.msg_idx = c.idx ";
	    
	    $sql .= $g_data["sql_where"];
	    
	    $sql .= " ORDER BY idx DESC " ;
	    $sql .= " LIMIT $start, ".$g_data['num_per_page'].";";
	    
	    $recordset = $this->select_query($sql);
	    
	    return $recordset;
	}
	
	public function getMsgSetList($g_data){
	    $sql  = "SELECT idx, a_id, title_view, title, content, send_cnt, use_kind, reg_time ";
	    $sql .= " FROM t_message_set ";
	    
	    $sql .= $g_data["sql_where"];
	    
	    $recordset = $this->select_query($sql);
	    
	    return $recordset;
	}
	
	public function getMsgSetInfo($g_data){
	    $sql = "SELECT  idx, a_id, title_view, title, content, send_cnt, use_kind, reg_time ";
	    $sql .= " FROM t_message_set ";
	    $sql .= " WHERE ";
	    $sql .= " idx=".$g_data['p_seq']."";
	    
	    if($g_data['p_seq']!='') {
	        //$sql .= " AND USE_KIND='".$g_data['p_use_kind']."'";
	    }
	    
	    
	    $recordset = $this->select_query($sql);
	    
	    return $recordset;
	}

	public function getTemplateListAll() {
		$sql = "SELECT a.idx, a.type, a.division, a.title FROM template AS a";
		return $this->select_query($sql);
	}

	public function getTemplateList($type) {
		$sql = "SELECT a.idx, a.division, a.title FROM template AS a WHERE a.`type` = $type";
		return $this->select_query($sql);
	}

	public function getTemplate($idx) {
		$sql = "SELECT a.idx, a.division, a.title, a.content FROM template AS a WHERE a.idx = $idx";
		return $this->select_query($sql);
	}
	
	public function getLogLoginList($g_data){
	    $start 	= ( $g_data['page'] -1 ) * $g_data['num_per_page'];
	    
	    $sql  = "SELECT b.idx, b.id, b.nick_name, b.status, a.idx as lidx, a.member_idx, a.login_domain ";
	    $sql .= " , a.ip, c.ip AS bip, c.idx as bidx, a.country, a.login_datetime ";
	    $sql .= " FROM member_login_history a LEFT JOIN member b ON a.member_idx = b.idx ";
	    $sql .= " LEFT JOIN member_ip_block_history c ON a.ip = c.ip ";
	    $sql .= $g_data["sql_where"];
	    
	    $sql .= " ORDER BY a.idx DESC " ;
	    $sql .= " LIMIT $start, ".$g_data['num_per_page'].";";
	    
	    $recordset = $this->select_query($sql);
	    
	    return $recordset;
	}
	
	//Message user
	public function setMsgSendList($g_data){
	    
	    $in_data = "'".$g_data['msg_key']."','".$g_data['msg_title']."','".$g_data['msg_content']."','".$g_data['aid']."'  ";
	    
	    $sql_1 = "INSERT INTO t_message_list (idx_key, title, content, a_id) VALUES($in_data)";
	    
	    //CommonUtil::logWrite($sql_1,"_msg_prc");
	    
	    if(FAIL_DB_SQL_EXCEPTION === $this->execute_query($sql_1)){
                throw new mysqli_sql_exception('mysqli_sql_exception!!!');
            }
	    
	    $sel_sql  = "SELECT idx FROM t_message_list ";
	    $sel_sql .= " WHERE idx_key='".$g_data['msg_key']."' ;";
	    
	    //CommonUtil::logWrite($sel_sql,"_msg_prc");
	    
	    $recordset = $this->select_query($sel_sql);
	    
	    return $recordset;
	}
	
	public function setMsgSendUser($g_data){
	    
	    if ($g_data['setUserType'] == 'alluser' ) {
	        
	        $sql  = "SELECT FN_ADM_MSG_SEND_ALL(".$g_data['msg_idx'].") as ret ";
	        
	        //CommonUtil::logWrite($sql,"_msg_prc");
	        
	        $recordset = $this->select_query($sql);
	        
	        return $recordset;
	    }
	    else {
	        $in_data = "".$g_data['msg_idx'].",".$g_data['member_idx']." ";
	        
	        $sql = "INSERT INTO t_message (msg_idx, member_idx) VALUES($in_data)";
	        
	        //CommonUtil::logWrite($sql,"_msg_prc");
	        
	        $recordset = $this->execute_query($sql);
	        
	        return $recordset;
	    }
	}
	
	public function setMemo($g_data) {
	    
	    if ($g_data['p_type'] == 'del' ) {
	        
	        $sql = "DELETE FROM t_member_memo WHERE idx = ".$g_data['memo_idx']." ";
	        
	        //CommonUtil::logWrite($sql,"pop_memo");
	        
	        $recordset = $this->execute_query($sql);
	        
	        return $recordset;
	    }
	    else if ($g_data['p_type'] == 'insert' ) {
	        $in_data = "".$g_data['m_idx'].",".$g_data['memo_type'].",'".$g_data['memo_title']."','".$g_data['a_id']."' ";
	        
	        $sql = "INSERT INTO t_member_memo (member_idx, m_type, content, a_id) VALUES($in_data)";
	        
	        //CommonUtil::logWrite($sql,"pop_memo");
	        
	        $recordset = $this->execute_query($sql);
	        
	        return $recordset;
	    }
	}
}
	
?>
		
