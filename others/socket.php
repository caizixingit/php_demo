<?php
function encode($data, $sep = '&')
{
	$result = [];
	foreach($data as $key => $value)
	{
		$result[] = urlencode($key). '='. urlencode($value);
	}
	return implode($sep, $result);
}

function post($url, $data, $cookie)
{
	$url = parse_url($url);
	$post = encode($data);
	$cookie = encode($cookie);

	$fp	= fsockopen($url['host'], $url['port'] ? $url['port'] : 80, $errno, $errstr, 10);

	$headers[] = sprintf("POST %s%s%s HTTP/1.1", $url['path'], "",  $url['query']);
	$headers[] = "Accept:image/gif,image/x-xbitmap,image/jpeg,application/x-shockwave-flash,application/vnd.ms-excel,application/vnd.ms-powerpoint,application/msword,*/*";
	$headers[] = "Host: ". $url['host'];
	$headers[] = "Content-type: application/x-www-form-urlencoded";
	$headers[] = "Content-length: ". strlen($post);
	$headers[] = "Cookie: ".$cookie;
	$headers[] = "Connection: close\r\n";
	$headers[] = $post;
	$headerStr = implode("\r\n", $headers);
	$headerStr .= "\r\n";
	fputs($fp, $headerStr);
	while(!feof($fp))
	{
		echo fgets($fp, 1024);
	}
	fclose($fp);
}
$url = 'http://172.16.2.201:8889/userAsync/Login';    
$post = array(    
		'loginName'=> '温柔一刀',    
		'password' => '&123=321&321=123&',    
		'key' => 'Hello world!'    
		);    
$cookie = array(    
		'cur_query' => 'you&me',    
		'last_tm' => time() - 600,    
		'cur_tm '=> time()    
		);   

post($url, $post, $cookie);
