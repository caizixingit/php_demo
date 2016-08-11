<?php
use base\Base;
use base\Reflect;


spl_autoload_register(array('Autoload', 'load'));



class Autoload
{
	static function load($class)
	{
		$name = str_replace('\\', '/', $class);
		require_once("{$name}.php");
	}
}

$reflect = new ReflectionClass('base\Reflect');
$obj = $reflect->newInstanceArgs();
