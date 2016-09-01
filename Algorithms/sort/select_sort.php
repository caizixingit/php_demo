<?php 
$arr = array(12,123,34,6,7,3,342,33,12);
select_sort($arr);
var_dump($arr);
function select_sort(&$arr)
{
	for($i = 0; $i < count($arr); $i++)
	{
		$min = $i;
		$min_value = $arr[$i];
		for($j = count($arr) - 1; $j > $i; $j--)
		{
			if($min_value > $arr[$j])
			{
				$min = $j;
				$min_value = $arr[$j];	
			}
		}
		if($min > $i)
		{
			$arr[$min] = $arr[$i];
			$arr[$i] = $min_value;
		}
	}
}
