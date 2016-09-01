<?php 
//基数排序
$arr = array(12,123,34,6,7,3,342,33,12);
radix_sort($arr);
var_dump($arr);
function radix_sort(&$arr)
{
	$times = get_max_dig($arr);
	if($times < 1)
		return false;
	foreach($arr as $k)
		$array[$k % 10][] = $k;
	for($i = 2; $i <= $times; $i++)
	{
		$new_array = array();
		for($j = 0; $j < 10; $j++)
		{
			for($k = 0; $k < count($array[$j]); $k++)
			{
				$num = get_dig_num($array[$j][$k], $i);
				$new_array[$num][] = $array[$j][$k];
			}
		}
		$array = $new_array;
	}
	$c = 0;
        for($j = 0; $j < 10; $j++)
        {       
        	for($k = 0; $k < count($array[$j]); $k++)
                {       
			echo $array[$j][$k]." ";
			$arr[$c++] =  $array[$j][$k];
                } 
	}	
}
function get_dig_num($num, $i)//获取数字num的第i位数字
{
	$mod = pow(10, $i);
	$div = $mod / 10;
	return intval(($num % $mod) / $div );
}
function get_max_dig($arr) // 获取最大的位数
{
	$max = 0;
	$count = 0;
	foreach($arr as $k)
	{
		if($k > $max)
			$max = $k;
	}
	while($max >= 1)
	{
		$max = $max / 10;
		$count++;
	}
	return $count;
}
?>
