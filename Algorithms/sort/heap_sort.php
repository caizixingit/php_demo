<?php 
$arr = array(12,123,34,6,7,3,342,33,12);
$heap = new Heap();
$heap->make_heap($arr);
	for($i = 0; $i < count($arr) - 1; $i++)
	{
		echo $heap->del(0)." ";	
	}

class Heap
{
	private $heap_arr = array();
	function __construct()
	{
		$this->heap_arr = array();
	}
	public	function make_heap($arr)
	{
		for($i = 0; $i < count($arr); $i++)
		{
			self::insert($arr[$i]);
		}
	}
	function insert($num)
	{
		$place = count($this->heap_arr);
		$this->heap_arr[$place] = $num;
		$i = $place;
		while($i > 0)
		{
			$j = ($i - 1) / 2;	
			$j = intval($j);
			if($this->heap_arr[$j] > $num)
			{
				$this->heap_arr[$i] = $this->heap_arr[$j];
				$this->heap_arr[$j] = $num;
				$i = $j;
			}
			else
			{
				break;
			}
		}
	}
	public function del($n)
	{
		$result = $this->heap_arr[$n];
		$count = count($this->heap_arr);
		while($n < $count)
		{
			$tmp1 = ($this->heap_arr[$n * 2 + 1] != null) ? $this->heap_arr[$n * 2 + 1] : 1000000; 
			$tmp2 = ($this->heap_arr[$n * 2 + 2] != null) ? $this->heap_arr[$n * 2 + 2] : 1000000;
			if($tmp1 < $tmp2)
			{
				$this->heap_arr[$n] = $tmp1;
				$n = $n * 2 + 1;
			}
			else
			{
				$this->heap_arr[$n] = $tmp2;
				$n = $n * 2 + 2; 
			}
		}
		return $result;
	}
	function get_arr()
	{
		return $this->heap_arr;
	}
	function test()
	{
		var_dump($this->heap_arr);
	}
	
}
