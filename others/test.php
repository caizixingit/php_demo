<?php  
class A{
	public $arr = array();
	public function __construct()
	{
		$this->arr[] = $this;	
	}

	public static function bbb()
	{
		return 'bbb';
	}

	public function getName()
	{
		foreach($this->arr as $one)
		{
			var_dump(get_class($this));
		}
	}

}

class B extends A
{
	public function __construct()
	{
		parent::__construct();
	}

	public function test()
	{
		unset($this->arr);
	}
}
$o = new B();
$o->test();
