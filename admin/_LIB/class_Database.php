<?php

set_time_limit(0);
ini_set('memory_limit', '-1');
ini_set('mysqli.default_socket', '/tmp/mysql5.sock');

class Database {

    public $connection = null;
    private $dbName = "";
    private $dbHost = "";
    private $dbPort = "";
    private $dbUser = "";
    private $dbPass = "";
    private $debug = false;
    private $dbDebug = true;
    private $stmt = null;
    private $tranChk = false;

    public function __construct($dbinfoname = null) {

        if (empty($dbinfoname))
            $dbinfoname = _DB_NAME_WEB;

        $this->dbName = $dbinfoname;
        $this->dbHost = _DB_IP;
        $this->dbPort = _DB_PORT;
        $this->dbUser = _DB_USER_WEB;
        $this->dbPass = _DB_PASS_WEB;

        if ($dbinfoname == _DB_NAME_LOG_DB) {
            $this->dbName = $dbinfoname;
            $this->dbHost = _DB_LOG_IP;
            $this->dbPort = _DB_LOG_PORT;
            $this->dbUser = _DB_USER_LOG;
            $this->dbPass = _DB_PASS_LOG;
            $this->debug = false;
        }
    }

    public function __destruct() {
                     
        if ($this->connection) {
            $this->disconnect();
        }
        
        
    }

    public function connect() {

        $this->connection = new mysqli($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName, $this->dbPort);

        if (mysqli_connect_error()) {
            CommonUtil::logWrite('false connect dbHost ==>' . $this->dbHost . ' dbUser==>' . $this->dbUser . ' dbPass==>' . $this->dbPass . ' dbName==>' . $this->dbName . ' dbPort==>' . $this->dbPort, "db_error");
            die();
            //return;
        } else {
            mysqli_set_charset($this->connection, "utf8");

            /**
              $this->query("SET NAMES UTF8");
              $this->query("set session character_set_connection=urf8");
              $this->query("set session character_set_results=urf8");
              $this->query("set session character_set_client=urf8");
             * */
        }

        mysqli_select_db($this->connection, $this->dbName);
        if (IMAGE_PATH === 'dev') {
            mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);
        } else {
            mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);
        }

        return $this->connection;
    }

    public function disconnect() {

        if ($this->connection) {
            $this->connection->close();
            $this->connection = null;
        }
        return true;
    }

    private function free() {
        $this->stmt->free_result();
    }

    private function prepare($query) {

        $this->stmt = $this->connection->prepare($query);
        $this->query = $query;
    }

    //public function bindParam($types, $var) {
    // The first argument of the array must be a data type. 'sss'
    private function bindParam($args) {

        //$args = func_get_args();
        if (0 == count($args))
            return;
        $str_type = '';
        foreach ($args as $value) {
            switch (gettype($value)) {
                case "integer": {
                        $str_type = $str_type . 'i';
                    }
                    break;
                case "string": {
                        $str_type = $str_type . 's';
                    }
                    break;

                case "double": {
                        $str_type = $str_type . 'd';
                    }
                    break;
                case "resource": {
                        $str_type = $str_type . 'b';
                    }
                    break;
            }
        }

        array_unshift($args, $str_type);

        if (!call_user_func_array(array($this->stmt, 'bind_param'), $this->retVal($args))) {
            $this->dbMsg();
        }
    }

    private function bindResult() {
        $retval = $this->bindExecute();
        if (FAIL_DB_SQL_EXCEPTION == $retval) {
            return FAIL_DB_SQL_EXCEPTION;
        }

        $dbRs = array();
        $meta = $this->stmt->result_metaData();
        $bindVars = array();

        while ($column = $meta->fetch_field()) {
            $bindVars[] = &$results[$column->name];
        }

        call_user_func_array(array($this->stmt, 'bind_result'), $bindVars);

        while ($this->stmt->fetch()) {
            $clone = array();
            foreach ($results as $k => $v) {
                $clone[$k] = $v;
            }
            $dbRs[] = $clone;
        }

        if (sizeof($dbRs) > 0) {
            return $dbRs;
        } else {
            return null;
        }
    }

    private function bindExecute() {
        try {
            $this->stmt->execute();
        } catch (mysqli_sql_exception $exc) {

            CommonUtil::logWrite('bindExecute Error No: ' . $exc->getCode() . ' - ' . $exc->getMessage(), "db_error");
            CommonUtil::logWrite('bindExecute ==>' . $exc->getTraceAsString(), "db_error");
            CommonUtil::logWrite('bindExecute query ==>' . $this->query, "db_error");
            return FAIL_DB_SQL_EXCEPTION;
        }
        return SUCCESS;
    }

    private function retVal($arr) {

        if (strnatcmp(phpversion(), '5.3') >= 0) { //Reference is required for PHP 5.3+
            $refs = array();
            foreach ($arr as $key => $value) {
                $refs[$key] = &$arr[$key];
            }

            return $refs;
        }

        return $arr;
    }

    public function getQueryData_pre($sql, $arg) {
        $this->prepare($sql);
        $this->bindParam($arg);
        $retData = $this->bindResult();
        //CommonUtil::logWrite("_login_prc prepare: " . json_encode($retData), "info");
        $this->free();
        return $retData;
    }

    public function setQueryData_pre($sql, $arg) {
        $this->prepare($sql);
        $this->bindParam($arg);
        $retval = $this->bindExecute();
        $this->free();
        return $retval;
    }

    public function select_query($query) {
        try {
            $result = $this->connection->query($query);
        } catch (mysqli_sql_exception $exc) {

            CommonUtil::logWrite('select_query Error No: ' . $exc->getCode() . ' - ' . $exc->getMessage(), "db_error");
            CommonUtil::logWrite('select_query ==>' . $exc->getTraceAsString(), "db_error");
            CommonUtil::logWrite('select_query query ==>' . $query, "db_error");
            return FAIL_DB_SQL_EXCEPTION;
        }

        if (!$result)
            return null;

        $rows = array();

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    private function select_assoc($query) {
        try {

            $result = $this->connection->query($query);
        } catch (mysqli_sql_exception $exc) {

            CommonUtil::logWrite('select_assoc Error No: ' . $exc->getCode() . ' - ' . $exc->getMessage(), "db_error");
            CommonUtil::logWrite('select_assoc ==>' . $exc->getTraceAsString(), "db_error");
            CommonUtil::logWrite('select_assoc query ==>' . $query, "db_error");
            return null;
        }

        if (!$result)
            return null;

        $rows = array();

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    private function select_total_count($table, $where = "") {

        $query = "SELECT COUNT(*) FROM " . $table . " " . $where;

        try {
            $result = $this->connection->query($query);
        } catch (mysqli_sql_exception $exc) {

            CommonUtil::logWrite('select_total_count Error No: ' . $exc->getCode() . ' - ' . $exc->getMessage(), "db_error");
            CommonUtil::logWrite('select_total_count ==>' . $exc->getTraceAsString(), "db_error");
            CommonUtil::logWrite('select_total_count query ==>' . $query, "db_error");
            return null;
        }

        if (!$result)
            return null;

        $rs = $result->fetch_row();

        return intval($rs[0]);
    }

    public function execute_query($query, $debug = 0) {
        try {
            $result = $this->connection->query($query);
        } catch (mysqli_sql_exception $exc) {

            CommonUtil::logWrite('execute_query Error No: ' . $exc->getCode() . ' - ' . $exc->getMessage(), "db_error");
            CommonUtil::logWrite('execute_query ==>' . $exc->getTraceAsString(), "db_error");
            CommonUtil::logWrite('execute_query query ==>' . $query, "db_error");
            return FAIL_DB_SQL_EXCEPTION;
        }

        return $result;
    }

    public function trans_start($auto_connect = true) {
        if ($auto_connect == true) {
            $this->connect();
        }

        if (!$this->connection || $this->connection->connect_errno) {
            return false;
        }

        $this->connection->query("SET AUTOCOMMIT=0");
        $this->connection->begin_transaction();
        $this->tranChk = true;
        return true;
    }

    public function commit() {
        if (!$this->connection || $this->connection->connect_errno) {
            return;
        }

        $this->connection->commit();
        $this->connection->query("SET AUTOCOMMIT=1");
        //$this->disconnect();
    }

    public function rollback() {
        if (!$this->connection || $this->connection->connect_errno) {
            return;
        }

        $this->connection->rollback();
        $this->connection->query("SET AUTOCOMMIT=1");
        //$this->disconnect();
    }

    public function real_escape_string($param) {
        if (!$this->connection || $this->connection->connect_errno) {
            return '';
        }

        return $this->connection->real_escape_string($param);
    }

    private function dbMsg() {

        if ($this->dbDebug == true) {
            echo "<style>td{font-size:9pt}</style>" .
            "<div id='dbErrDiv' style='z-index:99'>" .
            "<table cellpadding='6' align=center>" .
            "<tr><td bgcolor='black'><font color='white'><b>DB ERROR!!</b></font></td></tr>" .
            "<tr><td bgcolor='#EEEEEE'>ErrorCode : " . $this->connection->errno . "</td></tr>" .
            "<tr><td bgcolor='#EEEEEE'>" . $this->connection->error . "</td></tr>" .
            "<tr><td bgcolor='#EEEEEE'>" . $this->query . "</td></tr>" .
            "<tr><td bgcolor='#EEEEEE'>" . $_SERVER['SCRIPT_NAME'] . "</td></tr>" .
            "</table>" .
            "<table align='center'>" .
            "<tr><td><a href=\"javascript:history.back()\">[Before]</a></td></tr>" .
            "</table>" .
            "</div>";
        }

        if ($this->tranChk == true) {
            $this->Rollback();
        }

        $this->dbClose();
        exit;
    }
}

?>
