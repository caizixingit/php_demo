<?php
class A
{
	public function __construct($name)
	{
		$this->name = $name;
	}
	private $name;
}

class B extends A
{
	public function __construct($name)
	{
		parent::__construct($name);
	}

	public function test()
	{
		unset($this->name);
	}
	public function __unset($key)
	{
		echo 123;
	}
}

$obj = new B('cc');
$obj->test();
unset($obj->name);

