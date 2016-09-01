<?php
define(OSS_URL_HOST, 'http://www.gushidai.com.cn/');

$url = 'http://aliyun.baidu.coomsdf.com.cn/yonghu/123123.png';
$url = preg_replace('/^(http:\/\/)?([a-z]([a-z0-9\-]*[\.。]))+[a-z]+\//', OSS_URL_HOST, $url);

var_dump($url);
		?>
