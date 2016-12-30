<?php

/**
 * Created by PhpStorm.
 * User: caizixin
 * Date: 16/11/17
 * Time: 下午6:37
 */

$http = new swoole_http_server("0.0.0.0", 9501);

$http->on('request', function ($request, $response) {
	var_dump($request);
	$response->header("Content-Type", "text/html; charset=utf-8");
	$response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});

$http->start();