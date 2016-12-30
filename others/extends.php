<?php

class base
{
	private static $_ins = [];

	public function insert($name)
	{
		self::$_ins[] = $name;
	}

	public function get()
	{
		return self::$_ins;
	}
}
class a extends base{
}
class b extends base{
}

$a = new a();
$b = new b();
$a->insert('a');
var_dump($a->get());
$b->insert('b');
var_dump($b->get());

