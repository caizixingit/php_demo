<?php
require_once("./conf.php");

class Database {

    private $db_host;
    private $db_user;
    private $db_pwd;
    private $db_name;
    private $db_conn;

    private function Database($host=DB_HOST, $user=DB_USER, $pwd=DB_PASSWORD, $dbname=DB_NAME) {
        $this->db_host = $host;
        $this->db_user = $user;
        $this->db_pwd = $pwd;
        $this->db_name = $dbname;
    }    

    public static function getInstance() {
        if($self == null) {
            $self = new Database();
        }
        return $self;
    }

    public function testOK() {
        var_dump($this->query("select * from droid4x_ad_apps_info"));
        echo 'OK';
    }

    private function open() {
        $this->db_conn = mysql_connect($this->db_host, $this->db_user, $this->db_pwd);
        if(!$this->db_conn) {
            die("Could not connect: ".mysql_error());
        }
        mysql_select_db($this->db_name);
	mysql_query("SET CHARACTER SET utf8");
    }

    private function close() {
        mysql_close($this->db_name);
    }

    public function query($sql) {
        $this->open();
        $res = mysql_query($sql, $this->db_conn);
        $ret = array();
        while($record = mysql_fetch_array($res)) {
            $ret[] = $record;
        }
        $this->close();
        return $ret;
    }

    public function insert($sql) {
        $this->open();
        $res = mysql_query($sql, $this->db_conn);
        $this->close();
        return $res;
    }

}

//$db = Database::getInstance();
//$db->testOK();


