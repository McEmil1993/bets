<?php
class DatabaseConnection
{
	public function __construct($database_name)
    {
        $this->mysql_host       = _DB_IP;
        $this->mysql_user       = _DB_USER_ADMIN;
        $this->mysql_password   = _DB_PASS_ADMIN;
        $this->mysql_database   = $database_name;
        $this->mysql_port       = _DB_LOG_PORT;
        $this->mysql_connection = null;

        $this->connect();
    }
    public function connect()
    {
        $this->mysql_connection = new mysqli($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database, $this->mysql_port);
        $this->mysql_connection->set_charset('utf8');
    }
    public function getData($query)
    {
        
        $result = $this->mysql_connection->query($query);
        $return_data = null;

        if ($result && mysqli_num_rows($result) > 0)
        {
            $return_data = array();
            // output data of each row
            while($row = mysqli_fetch_assoc($result))
            {
                array_push($return_data, $row);
            }
        }

        return $return_data;
    }
    public function __destruct()
    {
        $this->mysql_connection->close();
    }
} 
?>