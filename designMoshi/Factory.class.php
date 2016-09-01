<?php
class A
{

}
class B
{

}
class Factory
{
    public static $instances = [];

    public static function getInstance($class)
    {
	    self::$instances[$class];
        if(!isset(self::$instances[$class]))
        {
	        self::$instances[$class] = new $class();
        }
        return self::$instances[$class];
    }

}
