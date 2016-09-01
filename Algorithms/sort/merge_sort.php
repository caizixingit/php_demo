<?php
	static $count = 0;
	$arr = array(22,32,45,6,2,4,23,89,32);
	mergesort($arr,0,count($arr) - 1);	
	var_dump($arr);
	echo $count; //逆序对
	function mergesort(&$arr, $l, $r)
	{
		$mid = intval(($l + $r ) / 2);
		if($mid > $l)	
			mergesort($arr, $l, $mid);
		if($r > ($mid + 1 ))
			mergesort($arr, $mid + 1, $r);
		merge($arr, $l, $mid, $r);
	}

	function merge(&$arr,$l, $mid, $r)
	{
		global $count;
		$j = $mid + 1;
		$i = $l;
		$tmp = array();
		while($i <= $mid && $j <= $r)
		{
			if($arr[$i] <= $arr[$j])
			{
				$tmp[] = $arr[$i];
				$i++;
			}
			else
			{
				$tmp[] = $arr[$j];
				$j++;
				$count += $mid - $i + 1;
			}
		}
		if($i <= $mid)
		{
			$start = $i;
			$end = $mid;
		}
		else if($j <= $r)
		{
			$start = $j;
			$end = $r;
		}
		for($start; $start <= $end; $start++)
			$tmp[] = $arr[$start];
		for($k = $l; $k <= $r; $k++)
			$arr[$k] = $tmp[$k - $l];
	}
?>
