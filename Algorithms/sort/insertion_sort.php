<?php 
$arr = array(12,123,34,6,7,3,342,33,12);
insertion_sort($arr);
var_dump($arr);
function insertion_sort(&$arr)
{
	for($i = 1; $i < count($arr); $i++)
	{
		$j = $i -1;
		$key = $arr[$i];
		while($j >= 0)
		{
			if($arr[$j] > $key)
			{
				$arr[$j+1] = $arr[$j];
				$j--;
			}
			else
				break;
		}
		$arr[$j+1] = $key;
	}
}

?>
