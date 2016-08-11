<?php

class API
{
	public function test($name, $option = "for")
	{
		return $name. ' '. $option. 'thy';
	}

	protected function client_can_not_see()
	{
	}
}

$service = new Yar_Server(new API());
$service->handle();
