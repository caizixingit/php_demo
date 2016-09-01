<?php

$pid = pcntl_fork();
if($pid == -1)
{
	echo 'open进程失败';
}
else if($pid > 0)
{
	$status = '';
	$pid = pcntl_wait($status);
	var_dump($pid, $status);
	exit;
}

pcntl_signal(SIGINT, 'handle');
pcntl_signal(SIGTERM, 'handleTerm');
pcntl_signal(SIGQUIT, 'handleQuit');

while(1)
{
	pcntl_signal_dispatch();
	sleep(1);
}

function handle($signal)
{
	var_dump($signal);
	exit;
}

function handleTerm($signal)
{
	var_dump('term', $signal);
	exit;
}

function handleQuit($signal)
{
	var_dump('quit', $signal);
	exit;
}