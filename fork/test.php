<?php
class A
{
	public function __construct()
	{
		$this->fd = fopen('123.txt', 'w+');
	}

	public function fork()
	{
		for($i = 0; $i < 100; $i++)
		{
			$pid = pcntl_fork();
			if($pid === 0)
			{
				flock($this->fd, LOCK_EX);
				fwrite($this->fd, "msg $i\n");		
				flock($this->fd, LOCK_UN);
				break;
			}
		}
		if($pic > 0)
			sleep(2);
		fclose($this->fd);
	}
}

$obj = new A();
$obj->fork();


