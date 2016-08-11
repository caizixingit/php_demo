<?
require_once("LibRedis.php");
echo time();
echo microtime(true);
echo "\n";
$config = array(
		"password" => "9110droid4xredis",
		"timeout" => "0.1",
		);
$redis = new LibRedis("/home/work/caizixin/workerman-chat-master/applications/Chat/Config/node_test", $config);
$arr = array("name"=>"caizixin","l"=>"thy");
$redis->set("caizixin", $arr);
var_dump($redis->watch("caizixin"));

$ret = $redis->getArr("caizixin");
sleep(30);
$arr['result'] = "suc";
$redis->setWatch("caizixin", $arr);
?>
