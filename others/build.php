<?php
require_once("DBProxy.class.php");
$file = "cmd.txt";
$sleepTime = 30;
$config = array(
				'hosts' => array("127.0.0.1"),
				'database' => "sche",
				'port' => 3306,
				'username' => 'root',
				'password' => 'haima',
				'timeout' => 1,
        );
while(1)
{
    $db = new DBProxy("sche", $config);

    $sql = "select * from sche_package where status in array(0, 3) limit 1";

    $ret = $db->queryFirstRow($sql);
    if(!is_array($ret) || empty($ret))
    {
        sleep(10);
        continue;
    }

    $cmd = $ret['cmd'];
    $md5 = $ret['md5'];

    if(empty($cmd) || strpos($cmd, "bat") === false)
    {
        sleep($sleepTime);
        continue;
    }
    $ret = shell_exec($cmd);
    if($ret == 0)
    {
        $sql = "update sche_package set status=1 where md5='{$md5}'";
        $ret = $db->doQuery($sql);
    }
    else
    {
        $sql = "update sche_package set status=3 where md5='{$md5}'";
        $ret = $db->doQuery($sql);
    }
    unset($db);
}
?>
