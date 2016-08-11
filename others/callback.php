<?php
require_once("DBProxy.class.php");
$config = array(
				'hosts' => array("127.0.0.1"),
				'database' => "sche",
				'port' => 3306,
				'username' => 'root',
				'password' => 'haima',
				'timeout' => 1,
        );
$md5 = $_GET['md5'];
$status =  $_GET['status'];
if(!in_array($status, array(2,3)) || empty($md5))
{
    echo "fail";
    exit();
}

$db = new DBProxy("sche", $config);

$sql = "update sche_package set status={$status} where md5='{$md5}'";
$ret = $db->doQuery($sql);
if($ret ==  false)
{
    echo "fail";
    exit();
}
echo "success";

?>
