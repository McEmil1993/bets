<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');

include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH.'/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');

include_once(_DAOPATH.'/class_Admin_Member_dao.php');


if (!isset($_SESSION)) {
    session_start();
}


$p_data['m_idx'] = trim(isset($_POST['m_idx']) ? $_POST['m_idx'] : 0);

$p_data['memo_idx'] = trim(isset($_POST['memo_idx']) ? $_POST['memo_idx'] : 0);

$p_data['p_type'] = trim(isset($_POST['p_type']) ? $_POST['p_type'] : 'list');

$p_data['memo_type'] = trim(isset($_POST['memo_type']) ? $_POST['memo_type'] : '');

$p_memo_title = trim(isset($_POST['memo_title']) ? $_POST['memo_title'] : '');
$p_data['memo_title'] = (urldecode($p_memo_title));

$p_data['page'] = trim(isset($_REQUEST['page']) ? $_REQUEST['page'] : 1);
if($p_data['page'] < 1) {
    $p_data['page'] = 1;
}

$p_data['num_per_page'] = trim(isset($_REQUEST['v_cnt']) ? $_REQUEST['v_cnt'] : 10);
if($p_data['num_per_page'] < 1) {
    $p_data['num_per_page'] = 10;
}

  $p_data['a_id'] = $_SESSION['aid'];
  

$data_str = "<table class='mlist'><tr><th>번호</th><th>구분</th><th>메모내용</th><th>일자</th><th>삭제</th></tr><tr><td colspan='5'>데이터가 없습니다.</td></tr></table>";

$MEMAdminDAO = new Admin_Member_DAO(_DB_NAME_WEB);
$db_conn = $MEMAdminDAO->dbconnect();

if($db_conn) {
    
    if ( ($p_data['p_type'] == 'del') || ($p_data['p_type'] == 'insert') ) {
        $MEMAdminDAO->setMemo($p_data);
    }
    
    
    $p_data["table_name"]=" t_member_memo ";
    $p_data["sql_where"]=" where member_idx=".$p_data['m_idx']." ";
    
    $db_total_cnt = $MEMAdminDAO->getTotalCount($p_data);
    
    $total_cnt = $db_total_cnt[0]['CNT'];
    //$p_data['page_per_block'] = _B_BLOCK_COUNT;
    $p_data['page_per_block'] = 5;
    
    $p_data['start'] = ($p_data['page']-1) * $p_data['num_per_page'];
    
    $total_page  = ceil($total_cnt/$p_data['num_per_page']);        // 페이지 수
    $total_block = ceil($total_page/$p_data['page_per_block']);     // 총 블럭 수
    $block		 = ceil($p_data['page']/$p_data['page_per_block']); // 현재 블럭
    $first_page  = ($p_data['page_per_block']*($block-1))+1;  	    // 첫번째 페이지
    $last_page 	 = $p_data['page_per_block']*$block;			    // 마지막 페이지
    
    if ($block >= $total_block) $last_page = $total_page;
    
    
    $p_data['sql'] = "select idx, m_type, content, a_id, reg_time from t_member_memo ";
    $p_data['sql'] .= $p_data["sql_where"];
    $p_data['sql'] .= " order by idx desc limit ".$p_data['start'].", ".$p_data['num_per_page']." ";
    
    $db_dataArr = $MEMAdminDAO->getQueryData($p_data);
    
    
    $MEMAdminDAO->dbclose();
    $i=0;
    if(!empty($db_dataArr)){
        $data_str = "<table class='mlist'><tr><th>번호</th><th>구분</th><th>메모내용</th><th>일자</th><th>삭제</th></tr>";
        
        foreach($db_dataArr as $row) {
            $m_type_str = "";

            $db_memo_idx = $row['idx'];
            
            switch ($row['m_type']) {
                case 1: $m_type_str = "일반메모"; $font_color = ''; break;
                case 2: $m_type_str = "정보변경"; $font_color = 'color:#0036FD'; break;
                case 3: $m_type_str = "보안주시"; $font_color = 'color:#FD0C00'; break;
            }
            
            //$log_str = "[_pop_memo_list] [".$row['m_type']."] [".$m_type_str."]";
            //$UTIL->logWrite($log_str,"pop_memo");
            
            $num = $p_data['num_per_page'] * ($p_data['page'] -1) + $i;
            
            $db_content = $row['content'];
            $db_date = $row['reg_time'];
            
            $data_str .= "<tr><td style='width: 70px;".$font_color."'>".($total_cnt-$num)."</td>";
            $data_str .= "<td style='width: 100px;".$font_color."'>".$m_type_str."</td>";
            $data_str .= "<td style='text-align:left;".$font_color."'>".$db_content."</td>";
            $data_str .= "<td style='width: 140px;".$font_color."'>".$db_date."</td>";
            $data_str .= "<td style='width: 80px;'><a href='javascript:getPopUserMemoList(\"del\",".$p_data['page'].", $db_memo_idx);' class='btn h25 btn_red' style='color:#fff'>삭제</a></td></tr>";
            
            
            $i++;
        }
        
        $data_str .= "</table>";
    }
}

$result['retCode']	= 1000;
$result['retData']	= $data_str;

//$log_str = "[_pop_memo_list] [".$result['retData']."]";
//$UTIL->logWrite($log_str,"pop_memo");

$str_page = "<div class='page'>";

if ($total_page == 0) {
    $str_page .= "<a href='javascript:;' class='none'><i class='mte i_navigate_before vam'></i></a>";
    $str_page .= "<a href='javascript:;' class='on'>1</a>";
    $str_page .= "<a href='javascript:;' class='none'><i class='mte i_navigate_next vam'></i></a>";
}
else {
    
    if ($block > 1) {
        $class_str_none_pre = "";
        $prv_b_page = $first_page-1;
        
    }
    else {
        $class_str_none_pre = "none";
        $prv_b_page = "1";
    }
    
    $str_page .= "<a href='javascript:getPopUserMemoList(\"list\",$prv_b_page, 0);' class='$class_str_none_pre'><i class='mte i_navigate_before vam'></i></a>";
    
    for($i=$first_page;$i<=$last_page; $i++) {
        if ($i == $p_data['page']) {
            $str_page .= "<a href='javascript:;' class='on'>$i</a>";
        }
        else {
            $str_page .= "<a href='javascript:getPopUserMemoList(\"list\",$i, 0);'>".$i."</a>";
        }
    }
    
    if ($block < $total_block) {
        $class_str_none_next = "";
        
        $next_b_page  = $last_page+1;
    }
    else {
        $class_str_none_next = "none";
        $next_b_page = "1";
    }
    
    $str_page .= "<a href='javascript:getPopUserMemoList(\"list\",$next_b_page, 0);' class='$class_str_none_next'><i class='mte i_navigate_next vam'></i></a>";
    
    
}


$str_page .= "</div>";

$result['retData']	.= $str_page;

//$log_str = "[_pop_memo_list] [".$result['retData']."]";
//$UTIL->logWrite($log_str,"pop_memo");

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>