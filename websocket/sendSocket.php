<?
// Run from command prompt > php -q chatbot.demo.php
include "WebSocket.php";
// Extended basic WebSocket as ChatBot
class ChatBot extends WebSocket{
	function process($user,$msg){

		if (isset($user->first)) {
			$this->send($user->socket,'');
			$user->first = true;
		} 

		$this->say("< ".$msg);
		switch($msg){
			case "hello" : $this->send($user->socket,"hello human");                       break;
			case "hi"    : $this->send($user->socket,"zup human");                         break;
			case "name"  : $this->send($user->socket,"my name is Multivac, silly I know"); break;
			case "age"   : $this->send($user->socket,"I am older than time itself");       break;
			case "date"  : $this->send($user->socket,"today is ".date("Y.m.d"));           break;
			case "time"  : $this->send($user->socket,"server time is ".date("H:i:s"));     break;
			case "thanks": $this->send($user->socket,"you're welcome");                    break;
			case "bye"   : $this->send($user->socket,"bye");                               break;
						   //default      : $this->send($user->socket,$msg." not understood");              break;
			default      : $this->sendAll($user, $msg);              break;
		}
	}
	function sendAll($currentUser, $msg){
		$usersList = $this->users;
		foreach ($usersList as $user){
			if ($user !== $currentUser) // 自己发送的消息就不再接收一次了
				$this->send($user->socket, $msg);
		}
	}
}
$master = new ChatBot("localhost",7272);
?>
