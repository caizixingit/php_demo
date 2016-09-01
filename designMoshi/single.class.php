<?
class Single
{
    private static $_instance = null;

    //设定为private， 那么不能new的方式初始化
    private function __construct()
    {
    
    }

    public static function getInstance()
    {
        if(self::$_instance instanceof self)
            return self::$_instance;
        else
        {
            self::$_instance = new self;
            return self::$_instance;
        }
    }

    public function __clone()
    {
        trigger_error('Clone is not allow!',E_USER_ERROR);
    }
}

$a = Single::getInstance();
$b = Single::getInstance();
$c = clone $a;
var_dump($a, $b, $c);
