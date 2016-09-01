<?php 
$arr = array(12,123,34,6,7,3,342,33,12);
bubble_sort($arr);
var_dump($arr);
function bubble_sort(&$arr)
{
	for($i = 0; $i < count($arr) - 1; $i++)
	{
		for($j = count($arr) - 1; $j > $i; $j--)
		{
			if($arr[$j] < $arr[$j - 1])
			{
				$key = $arr[$j];
				$arr[$j] = $arr[$j - 1];
				$arr[$j - 1] = $key;
			}
		}
	}	
}

?>
