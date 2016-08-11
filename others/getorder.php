<?php
error_reporting(E_ALL|E_STRICT);

$type = $_GET['reqType'];

$config = array(
				'hosts' => array("127.0.0.1"),
				'database' => "sche",
				'port' => 3306,
				'username' => 'root',
				'password' => 'haima',
				'timeout' => 1,
        );

$db = new DBProxy("sche", $config);

if($type == 0)
{
    $channel = $_GET['channel'];
    $packageType = $_GET['packageType'];
    $version=$_GET['version'];

    $cmd = "run.bat " . $channel . " " . $packageType . " " . $version;
    $key  = md5($cmd);
    $sql = "insert into sche_package (`cmd`, `md5`, `status`, `time`) values ('{$cmd}', '{$md5}', 0, '{$time}')";
    $ret = $db->doQuery($sql);
    if($ret == false)
    {
        echo "fail";
        exit();
    }
    echo $key;
    exit();
}

if($type == 1)
{
    $key = $_GET['key'];
    $sql = "select status from sche_package where cmd='{$key}'";
    $ret = $db->queryFirstRow($sql);
    if(!isset($ret['status']))
    {
        echo "fail";
        exit();
    }
    switch($ret['status'])
    {
        case 0:
            echo "not start";
            exit();
        case 1:
            echo "under doing";
            exit();
        case 2:
            echo "done";
            exit();
        case 3:
            echo "fail";
            exit();
    }

    echo "true";
    exit();
}
