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

define("GAMELIST", "droid_game_list"); //所有游戏列表
define("ROOMLIST_PREFIX", "droid_room_list-"); //一个游戏的房间列表
define("USERLIST_PREFIX", "droid_user_list-"); //一间房间的用户列表
define("ADDUSERLIST_PREFIX", "droid_adduser_list-"); //一间房间新增用户列表

define("MSGLIST_PREFIX", "droid_msg_list-"); //房间的消息列表

define("ROOM_USER_MAX", "droid_user_max"); //房间最大用户数量


$store = $redis;


$gameList = $store->getArr(GAMELIST);
$gameList = array(167);
foreach($gameList as $game)
{
    $key = ROOMLIST_PREFIX. $game;
    $addkey = ADDUSERLIST_PREFIX. $game;
    $userkey = USERLIST_PREFIX. $game;
    $roomList = $store->getLRange($key, 0, -1);
    if(!is_array($roomList))
        continue;
    foreach($roomList as $room)
    {
        $client_list = $store->getArr($userkey. "_". $room);
        $add_client_list = $store->getLRange($addkey. "_". $room, 0, -1);
        if(empty($add_client_list))
        {
            $add_client_list = array();
        }

        if($client_list === false)
        {
            throw new \Exception("Redis->get  false");
            continue;
        }

        if(!is_array($client_list))
        {
            throw new \Exception("Redis->getArr($key) return not array");
            continue;
        }

        foreach($add_client_list as $one)
        {
            $one =  unserialize(base64_decode($one));
            foreach($one as $k =>$v)
            {
                $client_list[$k] = $v;
            }
        }

        // 获取所有所有房间的实际在线客户端列表，以便将存储中不在线用户删除
        if($all_online_client_id && $client_list)
        {
            $flip_online_client_id = array_flip($all_online_client_id);
            $client_list = array_intersect_key($client_list, $flip_online_client_id);
        }
        $ret = false;
        if(is_array($client_list))
            $ret = $store->set($userkey."_".$room, $client_list);
        if($ret === false)
        {
            throw new \Exception("Redis->set($key) fail");
        }
       // $ret = $store->setlTrim($addkey. "_". $room, -1, 0);
        //删除key
        $ret = $store->delete($addkey. "_". $room);
        $client_id_array = array_keys($client_list);
        $new_message = array('type'=>'new_list','list'=>$client_list);
    }
}

?>
