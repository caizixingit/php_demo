<?
require_once("LibRedis.php");
echo time();
echo microtime(true);
echo "\n";
$arr = array();
for($i = 0; $i < 10000; $i++)
{
	$arr[] = $i."csdfdsfs";
}
$config = array(
		"password" => "9110droid4xredis",
		"timeout" => "0.1",
		);
$redis = new LibRedis("/home/work/caizixin/workerman-chat-master/applications/Chat/Config/node_test", $config);
$time1 = microtime(true);
$ret = $redis->getLrange("DROID_MSG_LIST-1",0, 1000);
foreach($ret as $k => &$one)
{
	$one = unserialize(base64_decode($one));
}
file_put_contents("111", var_export($ret,1));

$time2 = microtime(true);
var_dump($time2, $time1);
var_dump($time2-$time1);
?>
