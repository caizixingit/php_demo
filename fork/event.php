<?php
require_once('EventInterface.php');
require_once('Libevent.php');
class Server
{
	private $_pids = [];
	public function __construct()
	{
		register_shutdown_function([$this, 'shutDown']);
		$this->fd = fopen('123.txt', 'w+');
		$this->socket = stream_socket_server("tcp://0.0.0.0:13008", $errno, $errmsg, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN);
		stream_set_blocking($this->socket, 0);
		//$socket = socket_import_stream($this->socket);
		//@socket_set_option($socket, SOL_SOCKET, SO_KEEPALIVE, 1);
		//@socket_set_option($socket, SOL_TCP, TCP_NODELAY, 1);
	}

	public function acceptConnection($socket)
	{
		$new_socket = @stream_socket_accept($socket, 0);
		if(!$new_socket)
		{
			return;
		}
		echo 'accept connection';
		$len = @fwrite($new_socket, $this->num. ' connect success!');
		echo "send message len({$len})\n";
		//fclose($this->new_socket);
//		$this->event->add($this->new_socket, EventInterface::EV_READ, array($this, 'baseRead'));
//		$this->event->add($this->new_socket, EventInterface::EV_WRITE, array($this, 'baseWrite'));
	}

	public function baseWrite()
	{
		fwrite($this->new_socket, rand(1, 100));	
	}

	public function baseRead()
	{
		$data = '';
		var_dump($this->num);
		while($buffer = fread($this->new_socket, 1024))
		{
			sleep(1);
			var_dump($buffer);
			$data .= $buffer;
			if($buffer == '111')
				break;
		}
		fwrite($this->fd, $data);
		fclose($this->fd);
//		fclose($this->new_socket);
	}

	public function fork()
	{
		for($i = 0; $i < 3; $i++)
		{
			$pid = pcntl_fork();
			if($pid === 0)
			{
				echo "$i \n";
				$this->num = $i;
				$this->event = new Libevent();
				$this->event->add($this->socket, EventInterface::EV_READ, array($this, 'acceptConnection'));
				$this->event->loop();
				break;
			}
			else
			{
				$this->_pids[] = $pid;
			}
		}

		$this->monitor();
	}

	public function monitor()
	{
		while(1)	
		{
			sleep(1);
		}
	}

	public function shutDown()
	{
		echo "start shutdown\n";
		foreach($this->_pids as $pid)
		{
			posix_kill($pid, 0);
		}
	}
}

$obj = new Server();
$obj->fork();


