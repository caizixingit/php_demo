<?php

class Base
{
	public function getName()
	{
		echo 'base';
	}
}

class TestA extends Base
{
	public function getName()
	{
		echo 'testA';
	}
}

class TestB extends Base
{
	public function getName()
	{
		echo 'testB';
	}
}


function test(Base $a)
{
	$a->getName();
}


$a = new testA();
test($a);
