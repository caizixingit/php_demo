<?php
require('mongoFactory.php');

$mongo = mongoProxy::getInstance('test');
$result = $mongo->db->user->find()->snapshot();
var_dump($result);
$result = $mongo->db->user->findOne();
var_dump($result);

