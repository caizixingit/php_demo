<?php
$http_proxy = getenv("HTTP_PROXY");
var_dump($http_proxy);die;
if ($http_proxy) {
	$context = array(
			'http' => array(
				'proxy' => $http_proxy,
				'request_fulluri' => true,
				),

			);
	$s_context = stream_context_create($context);
} else {
	$s_context = NULL;
}
$ret = file_get_contents("http://www.laruence.com/", false, $s_context);
