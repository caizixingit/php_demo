<?php

function createGreeter($who) {
	return function() use ($who) {
		echo "Hello $who\n";
	};
}

function demo1()
{
	$greeter = createGreeter("World");
	$greeter(); // Hello World
	var_dump($greeter instanceof Closure);
}


function demo2()
{
	$name = 'tanghuiyan';
	$func = function ($for) use(&$name)
	{
		return $name. ' for '. $for; 
	};

	var_dump($func('czx'));
	$name = 'thy';
	var_dump($func('czx'));
}

function demo3()
{
}
