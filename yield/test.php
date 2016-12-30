<?php
/**
 * Created by PhpStorm.
 * User: caizixin
 * Date: 16/11/18
 * Time: 下午12:00
 */
/*
function gen() {
	$ret = (yield 'yield1');
	var_dump($ret);
	$ret = (yield 'yield2');
	var_dump($ret);
}

$gen = gen();
var_dump($gen->current());    // string(6) "yield1"
var_dump($gen->send('ret1')); // string(4) "ret1"   (the first var_dump in gen)
                              // string(6) "yield2" (the var_dump of the ->send() return value)
var_dump($gen->send('ret2')); // string(4) "ret2"   (again from within gen)
                              // NULL               (the return value of ->send())

*/
function gen() {
	yield 'foo';
	yield 'bar';
}

$gen = gen();
//var_dump($gen->current());
var_dump($gen->send('something'));

$i = 0;
for(;;)
{
	echo $i++;
	if($i >= 100)
		break;
}

