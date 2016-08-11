<?php
namespace base;
abstract class Base
{
	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
	}
}
