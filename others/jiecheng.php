<?php
for($i = 1; $i < 10000000; $i++)
{
	$tmp = (string) $i;
	$sum = 0;
	for($j = 0; $j < strlen($tmp); $j++)
		$sum += jiecheng($tmp[$j]);

	if($sum === $i)
		echo $i. "\n";
}

function jiecheng($i)
{
	$result = 1;
	for($i; $i > 0; $i--)
		$result *= $i;
	return $result;
}
