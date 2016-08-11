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
for($i = 1; $i <= 200; $i++)
{
    $redis->delete($roomKey. $i);
}
$list = $redis->getArr($gameKey);
$new_list = array();
foreach($list as $one)
{
	if(!is_numeric($one))
	{
		$new_list[] = $one;
	}
}
$redis->set($gameKey, $new_list);

?>
