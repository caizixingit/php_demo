<?php

$now = 5;
$pinggai = 0;
$ping = 0;
$total = 0;
while($now > 0 || $pinggai >= 4 || $ping >= 2)
{
	echo "酒:$now, 瓶盖:$pinggai, 瓶:$ping 总计:$total\n";
	if($now > 0)
	{
		$now--;
		$ping++;
		$pinggai++;
		$total++;
	}

	if($pinggai >= 4)
	{
		$pinggai -= 4;
		$now++;
	}

	if($ping >= 2)
	{
		$ping -= 2;
		$now++;
	}
}

echo "酒:$now, 瓶盖:$pinggai, 瓶:$ping 总计:$total\n";
