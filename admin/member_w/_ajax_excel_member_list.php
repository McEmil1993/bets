<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/_LIB/base_config.php');
include_once(_BASEPATH.'/common/_common_inc_class.php');
include_once(_BASEPATH . '/common/auth_check.php');
include_once(_DAOPATH.'/class_Admin_Common_dao.php');

try {
    $result['retCode'] = SUCCESS;
    $result['retMsg'] = 'success';

    /*$status = trim(isset($_REQUEST['status']) ? $_REQUEST['status'] : 0);
    $member_id = trim(isset($_REQUEST['member_id']) ? $_REQUEST['member_id'] : '');

    if (!is_int((int)$status) || !is_string($srch_s_date) || !is_string($srch_e_date)) {
        CommonUtil::logWrite("excel_exchange_list error param ", "error");
        $result['retCode'] = FAIL_PARAM;
        $result['retMsg'] = FAIL_PARAM_MSG;
        return;
    }*/

    $commonDAO = new Admin_Common_DAO(_DB_NAME_WEB);
    $db_conn = $commonDAO->dbconnect();

    if (!$db_conn) {
        CommonUtil::logWrite("_ajax_excel_member_list db_conn ", "error");
        $result['retCode'] = FAIL_DB_CONNECT;
        $result['retMsg'] = FAIL_DB_CONNECT_MSG;
        return;
    }

    // 입금계좌 정보
    $p_data['sql'] = "SELECT IFNULL(a.dis_id,'') as '가입총판', a.level as '레벨', a.id as '아이디', a.nick_name as '닉네임', a.account_name as '이름', a.call as '연락처',
            a.account_number as '계좌번호',  a.reg_time as '가입일',
            IFNULL((IFNULL(t_m_ch.charge_total_money,0) + IFNULL(ch.money,0)),0) AS '총입금금액' , 
            IFNULL((IFNULL(t_m_ch.exchange_total_money,0) + IFNULL(ex.money,0)),0) AS '총출금금액'";
    $p_data['sql'] .= " FROM member a "
            . "left join total_member_cash as t_m_ch ON a.idx = t_m_ch.member_idx "
            . "left join ( SELECT IFNULL(SUM(c.money),0) as money, c.member_idx FROM member_money_charge_history c ";
    $p_data['sql'] .= "left join member t1 on c.member_idx=t1.idx "
            . "WHERE c.status=3 AND c.update_dt >= DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00') and c.update_dt <= NOW() group by t1.idx ) as ch "
            . "ON a.idx = ch.member_idx "
            . "left join ( SELECT IFNULL(SUM(e.money),0) as money,e.member_idx FROM member_money_exchange_history e "
            . "left join member t1 on e.member_idx=t1.idx "
            . "WHERE e.status=3 and e.update_dt >= DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00') and e.update_dt <= NOW() group by t1.idx ) as ex ON a.idx = ex.member_idx "
            . "WHERE a.status = 1 and a.u_business = 1 and a.level <> 9;";

    $db_dataArr = $commonDAO->getQueryData_pre($p_data['sql'], []);
    
    if (FAIL_DB_SQL_EXCEPTION === $db_dataArr) {
        CommonUtil::logWrite("[_ajax_excel_member_list] db_dataArr ", "error");
        throw new mysqli_sql_exception('mysqli_sql_exception!!!');
    }
        
    $db_dataArr = is_null($db_dataArr) ? [] : $db_dataArr;
    $result['data_list'] = $db_dataArr;
} catch (\mysqli_sql_exception $e) {
    CommonUtil::logWrite('[MYSQL EXCEPTION] _ajax_excel_member_list mysqli_sql_exception (code) : ' . $e->getMessage() . ' (' . $e->getCode() . ')', "error");

    $result['retCode'] = FAIL_DB_SQL_EXCEPTION;
    $result['retMsg'] = FAIL_DB_SQL_EXCEPTION_MSG;
} catch (\Exception $e) {
    CommonUtil::logWrite('::::::::::::::: _ajax_excel_member_list Exception : ' . $e->getMessage(), "error");

    $result['retCode'] = FAIL_EXCEPTION;
    $result['retMsg'] = FAIL_EXCEPTION_MSG;
} finally {

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}
?>