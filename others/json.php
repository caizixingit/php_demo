<?php
$test = '[
{
	"id": "2",
		"name": "a",
		"pkg": "asdfsdf.sdfsd.sdf",
		"image": "http://img.haima.me/andr/acf/cdb/d4x/img_1427361321_6788.png",
		"description": "asdfsadf",
		"link": "http://www.haimawan.com",
		"starttime": "1427212800",
		"endtime": "1437062400"
}
]';
var_dump(json_decode($test, 1));


$obj = new stdClass();
$obj->abc = 'abc';
$obj->ddd = 'dd';
$arr['list'] = $obj;

echo json_encode($arr);die;

