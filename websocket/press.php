<?
require_once("WebSocket.php");
class WebSocket
{
	function __construct()
	{
		$this->host = '172.16.12.181';  //where is the websocket server
		$this->port = 7272;
		$this->local = "http://172.16.12.181";  //url where this script run
		$this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$conn = socket_connect($this->sock, $this->host, $this->port);
		$this->result = array();
	}
	//握手，验证握手，登陆，验证登陆
	function login()
	{
		$this->name = $this->getRandomName();
		$data['type'] = 'login';
		$data['client_name'] = $this->name;
		$data['room_id'] = 1;
		$data = json_encode($data);

		$head = "GET / HTTP/1.1"."\r\n".
			"Upgrade: WebSocket"."\r\n".
			"Connection: Upgrade"."\r\n".
			"Origin: $this->local"."\r\n".
			"Host: $this->host"."\r\n".
			"Sec-WebSocket-Key: asdasdaas76da7sd6asd6as7d"."\r\n".
			"Content-Length: ".strlen($data)."\r\n"."\r\n";
		//WebSocket handshake
		$ret = socket_write($this->sock, $head, strlen($head) );
		if($ret === false)
		{
//			echo "write error\n";
			return false;
		}
		$buffer = socket_read($this->sock, 2048, PHP_BINARY_READ);
		$time = time();
		while(strpos($buffer, "Sec-WebSocket-Accept") === false)
		{
			$nowtime = time();
			if( ($nowtime - $time) > 4)
			{
//				echo "hand error\n";
				return false;
			}
			$buffer = socket_read($this->sock, 2048,PHP_BINARY_READ);
		}

		$data = $this->hybi10Encode($data);
		socket_write($this->sock, $data, strlen($data) );
		$buffer = socket_read($this->sock, 2048, PHP_BINARY_READ);
		$data = json_decode($this->hybi10Decode($buffer), 1);	
		$time = time();
		while($data['type'] != 'login' || $data['client_name'] != $this->name)
		{
			$nowtime = time();
			if( ($nowtime - $time) > 4)
			{
		//		echo "login error\n";
	//			return false;
				return true;
			}
			$buffer = socket_read($this->sock, 2048, PHP_BINARY_READ);
			$data = json_decode($this->hybi10Decode($buffer), 1);	
			if(!is_array($data))
			{
		//		echo "login array error\n";
			}
		}
		return $data['client_name'];
	}

	function sendMsg($msg)
	{
		$data['type'] = 'say';
		$data['to_client_id'] = 'all';
		$data['to_client_name'] = "所有人";
		$data['content'] = $msg;
		$data = json_encode($data);
	
		$data=$this->hybi10Encode($data);
		$ret = socket_write($this->sock, $data, strlen($data));
		if($ret === false)
		{
			echo "msg write error\n";
			return ;
		}
		$buffer = socket_read($this->sock, 2048, PHP_BINARY_READ);
		$data = json_decode($this->hybi10Decode($buffer), 1);	
		$time = time();
		while($data['type'] != "say" || $data['from_client_name'] != $this->name)
		{
			$nowtime = time();
			if( ($nowtime - $time) > 4)
			{
		//		echo "say error\n";
		//		return false;
			}
			$buffer = socket_read($this->sock, 2048, PHP_BINARY_READ);		
			$data = json_decode($this->hybi10Decode($buffer), 1);	
			if(!is_array($data))
			{
		//		echo "say array error\n";
		//		var_dump($this->hybi10Decode($buffer));
			}
		}
	}
	
	function onclose()
	{
		$str =  implode("\n", $this->result);
		file_put_contents("pressLog", $str. "\n", FILE_APPEND);
		socket_close($this->sock);
	}

	function getRandomName()
	{
		$count = rand(3,8);
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($strPol) - 1;
		$name = '';
		for($i=0; $i < $count; $i++)
		{
			$name .= $strPol[rand(0, $max)];
		}
		return $name;
	}

	function hybi10Decode($data)
	{
		$bytes = $data;
		$dataLength = '';
		$mask = '';
		$coded_data = '';
		$decodedData = '';
		$secondByte = sprintf('%08b', ord($bytes[1]));
		$masked = ($secondByte[0] == '1') ? true : false;
		$dataLength = ($masked === true) ? ord($bytes[1]) & 127 : ord($bytes[1]);

		if($masked === true)
		{
			if($dataLength === 126)
			{
				$mask = substr($bytes, 4, 4);
				$coded_data = substr($bytes, 8);
			}
			elseif($dataLength === 127)
			{
				$mask = substr($bytes, 10, 4);
				$coded_data = substr($bytes, 14);
			}
			else
			{
				$mask = substr($bytes, 2, 4);       
				$coded_data = substr($bytes, 6);        
			}   
			for($i = 0; $i < strlen($coded_data); $i++)
			{       
				$decodedData .= $coded_data[$i] ^ $mask[$i % 4];
			}
		}
		else
		{
			if($dataLength === 126)
			{          
				$decodedData = substr($bytes, 4);
			}
			elseif($dataLength === 127)
			{           
				$decodedData = substr($bytes, 10);
			}
			else
			{               
				$decodedData = substr($bytes, 2);       
			}       
		}   

		return $decodedData;
	}


	function hybi10Encode($payload, $type = 'text', $masked = true) {
		$frameHead = array();
		$frame = '';
		$payloadLength = strlen($payload);

		switch ($type) {
			case 'text':
				// first byte indicates FIN, Text-Frame (10000001):
				$frameHead[0] = 129;
				break;

			case 'close':
				// first byte indicates FIN, Close Frame(10001000):
				$frameHead[0] = 136;
				break;

			case 'ping':
				// first byte indicates FIN, Ping frame (10001001):
				$frameHead[0] = 137;
				break;

			case 'pong':
				// first byte indicates FIN, Pong frame (10001010):
				$frameHead[0] = 138;
				break;
		}

		// set mask and payload length (using 1, 3 or 9 bytes)
		if ($payloadLength > 65535) {
			$payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
			$frameHead[1] = ($masked === true) ? 255 : 127;
			for ($i = 0; $i < 8; $i++) {
				$frameHead[$i + 2] = bindec($payloadLengthBin[$i]);
			}

			// most significant bit MUST be 0 (close connection if frame too big)
			if ($frameHead[2] > 127) {
				$this->close(1004);
				return false;
			}
		} elseif ($payloadLength > 125) {
			$payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
			$frameHead[1] = ($masked === true) ? 254 : 126;
			$frameHead[2] = bindec($payloadLengthBin[0]);
			$frameHead[3] = bindec($payloadLengthBin[1]);
		} else {
			$frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
		}

		// convert frame-head to string:
		foreach (array_keys($frameHead) as $i) {
			$frameHead[$i] = chr($frameHead[$i]);
		}

		if ($masked === true) {
			// generate a random mask:
			$mask = array();
			for ($i = 0; $i < 4; $i++) {
				$mask[$i] = chr(rand(0, 255));
			}

			$frameHead = array_merge($frameHead, $mask);
		}
		$frame = implode('', $frameHead);
		// append payload to frame:
		for ($i = 0; $i < $payloadLength; $i++) {
			$frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
		}

		return $frame;
	}
}
/*
file_put_contents("pressLog", "");
for($i=0; $i<1;$i++)
{
$obj = new WebSocket();
$obj->login();
for($j=0; $j<1; $j++)
	$obj->sendMsg($j);
$obj->onclose();
}
exit;
*/



$workers = 10;
file_put_contents("pressLog", "");
$pids = array();
for($i = 0; $i < $workers; $i++)
{
	$objs[$i] = new WebSocket();
	$ret = $objs[$i]->login();
}
sleep(20);
for($i = 0; $i < $workers; $i++){
	$pids[$i] = pcntl_fork();
	switch ($pids[$i]) {
		case -1:
			echo "fork error : {$i} \r\n";
			exit;
		case 0:
			echo posix_getpid()."\n";
			if($ret !== false)
			{
				for($j = 0; $j < 10; $j++)
				{
					$objs[$j]->sendMsg($j);
				}

			}
			$obj->onclose();
			exit;
		default:
			break;
	}
}

foreach ($pids as $i => $pid) {
	if($pid) {
		pcntl_waitpid($pid, $status);
	}
}
?>
