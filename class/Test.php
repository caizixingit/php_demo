<?php 

require('Pimple.php');

class A{
}

class B{
}

class C{
}

class Test extends Pimple
{
	public function __construct()
	{
		$app = $this;
		$this['routes'] = $this->share(function () {
				return new A();
				});

		$this['controllers'] = $this->share(function () use ($app) {
				return $app['controllers_factory'];
				});

		$this['controllers_factory'] = function () use ($app) {
			return new B();
		};
	}


}


$obj = new Test();
var_dump($obj['routes']);
var_dump($obj['controllers']);
var_dump($obj['controllers_factory']);

