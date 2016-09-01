<?php 
$arr = array(12,123,34,6,7,3,342,33,12);
shell_sort($arr);
var_dump($arr);
function shell_sort(&$arr)
{
	$d = count($arr);
	while($d > 1)
	{
		$d = intval($d / 2);
		for($i = 0; $i < $d; $i++)
		{
			insert_sort($arr, $i, $d);
		}
	}
}

function insert_sort(&$arr, $s, $d)
{
	$count = count($arr);
	for($i = $s + $d; $i < $count; $i += $d )
	{
		$j = $i - $d;
		$key = $arr[$i];
		while($j >= 0)
		{
			if($arr[$j] > $key)
			{
				$arr[$j + $d] = $arr[$j];
				$j = $j - $d;
			}
			else
				break;
		}
		$arr[$j + $d] = $key;
	}
}

?>
