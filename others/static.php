<?php
class A
{
	public static function get()
	{
		return get_class();
	}
	public static function getClass()
	{
		return static::get();
	}
}

class B extends A
{
	public static function get()
	{
		return get_class();
	}

}
var_dump(B::getClass());
