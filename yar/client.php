<?php
/**
 * Created by PhpStorm.
 * User: caizixin
 * Date: 16/8/11
 * Time: 下午5:52
 */
$client = new Yar_Client('http://182.92.170.3:9990');
$result = $client->test('caizixin');
var_dump($result);
