<?php
require_once('EventInterface.php');
require_once('Libevent.php');
class A
{
	public function __construct()
	{
		$this->fd = fopen('123.txt', 'w+');
		$this->socket = stream_socket_server("tcp://127.0.0.1:13008", $errno, $errmsg);
	}

	public function acceptConnection()
	{
		$this->new_socket = @stream_socket_accept($this->socket, 0);
		$this->event->add($this->new_socket, EventInterface::EV_READ, array($this, 'baseRead'));
	}

	public function baseRead()
	{
		$data = '';
		var_dump($this->num);
		while($buffer = fread($this->new_socket, 1024))
		{
			var_dump($buffer);
			$data .= $buffer;
			if($buffer == '111')
				break;
		}
		fwrite($this->fd, $data);
		fclose($this->fd);
		fclose($this->new_socket);
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
		}
	}
}

$obj = new A();
$obj->fork();


