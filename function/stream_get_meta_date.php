<?php

$url = 'http://www.sina.com.cn';
$hl = fopen($url, 'r');
$meta = stream_get_meta_data($hl);

while(!feof($hl))
{
	$data .= fgets($hl, 1024);
}

var_dump($meta);
var_dump($data);
