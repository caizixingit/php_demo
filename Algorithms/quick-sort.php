<?php
function quick($arr)
{
	if(count($arr) <= 1)
		return $arr;
	$key = $arr[0];
	$right = [];
	$left = [];
	foreach($arr as $one)
	{
		if($one > $key)
			$right[] = $one;
		elseif($one < $key)
			$left[] = $one;
	}

	$left = quick($left);
	$right = quick($right);

	return array_merge($left, array($key), $right);
}

$arr = array(22,32,45,6,2,4,23,89,32);
var_dump(quick($arr));
