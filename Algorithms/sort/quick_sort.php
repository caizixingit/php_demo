<?php
	$arr = array(22,32,45,6,2,4,23,89,32);
	quicksort($arr,0,count($arr) - 1);	
	var_dump($arr);
	function quicksort(&$arr, $l, $r)
	{
		if($l == $r)  return;
		$mid = intval( ($l + $r) / 2);
		$key = $arr[$mid];
		$i = $l;
		$j = $r;
		while($i < $j)
		{
			for($i; $i < $j; $i++)
			{
				if($arr[$i] >= $key)
					break;
			}
			for($j; $j > $i; $j--)
			{		
				if($arr[$j] <= $key && $arr[$j] != $arr[$i])  //后一项用于判重
					break;
			}
			if($j > $i)
			{	
				$temp = $arr[$j];
				$arr[$j] = $arr[$i];
				$arr[$i] = $temp;
			}
		}
		if($i == $j)
		{
			if($l < $j)
				quicksort($arr,$l, $j);
			if($r > $i)
				quicksort($arr,$i+1, $r);
		}		
	}
?>
