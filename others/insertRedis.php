<?
require_once("LibRedis.php");
$userKey = "droid_user_list-";
$gameKey = "droid_game_list";
$roomKey = "droid_room_list-";
$config = array(
		"password" => "9110droid4xredis",
		"timeout" => "0.1",
		);
$redis = new LibRedis("/home/work/caizixin/workerman-master/applications/Chat/Config/node_store_test", $config);

//$ret = $redis->getArr('droid_user_list-haimawan_child1');


$redis->set("droid_user_max", 500);
//die;
for($i = 1; $i <= 200; $i++)
{
    //游戏区
    //设置子房间1为空房间
    $ret[] = $redis->set($userKey. $i. "_child1", array());

    //该游戏有一个子房间为_child1
    $redis->delete($roomKey. $i);
    $newret[] = $redis->push($roomKey. $i,  "child1", 'rpush');
    $list[] = $i;
}
$redis->set($gameKey, $list);
var_dump($ret);

?>
