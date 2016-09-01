<?php
class A
{
	public static $ins = ['a' => 'a1', 'b' => 'b1'];
	public static function getIns()
	{
		return $ins['a'];	
	}
}

$arr = ['ABC', 'DDD'];

array_walk($arr, 'test', 'abc');

function test(&$value, $key, $ext)
{
	$value .= $key. $ext;
}

var_dump($arr);
