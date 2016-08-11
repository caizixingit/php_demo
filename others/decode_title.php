<?php
$content = file_get_contents('title_cc.txt');
$list = explode("\n", $content);
foreach($list as $one)
{
	$one = trim($one);
	$decode = decode($one);
	$str = $one. "\t". $decode;
	$result[] = $str;
}

$content = implode("\r\n", $result);
file_put_contents("title_decode.txt", $content);

function decode($str)
{
	return urldecode($str);
}
