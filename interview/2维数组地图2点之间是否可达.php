<?php
/**
 * Created by PhpStorm.
 * User: caizixin
 * Date: 17/3/7
 * Time: 下午5:46
 */

/**
  一个二维数组(m*n)地图,其中0表示可走,1表示不可以达,实例如下所示，判断其中起始点([0,0])能否到达终点([m,n])
 *  [
 *      0，0，0，0，0
 *      0，1，0，0，0
 *      0，1，0，0，0
 *      0，1，0，0，0
 *      0，1，0，0，0
 *  ]
 */


function dfs($map, $arriving = [], $i = 0, $j = 0)
{
	$line = count($map);
	$column = count($map[0]);

	if($i == ($line - 1) && $j == ($column - 1))
	{
		echo "success\n";
		exit;
	}
	$arriving[$i][$j] = 1;

	if($i + 1 < $line && $map[$i + 1][$j] == 0 && !isset($arriving[$i + 1][$j]))
	{
		dfs($map, $arriving, $i + 1, $j);
	}
	if($j + 1 < $column && $map[$i][$j + 1] == 0 && !isset($arriving[$i][$j + 1]))
	{
		dfs($map, $arriving, $i, $j + 1);
	}
	if($i - 1 > 0 && $map[$i - 1][$j] == 0 && !isset($arriving[$i - 1][$j]))
	{
		dfs($map, $arriving, $i - 1, $j);
	}
	if($j - 1 > 0 && $map[$i][$j - 1] == 0 && !isset($arriving[$i][$j - 1]))
	{
		dfs($map, $arriving, $i, $j - 1);
	}
}

$map = [
	[0, 0, 0, 0, 1],
	[0, 1, 1, 0, 0],
	[0, 1, 0, 1, 0],
	[0, 1, 0, 0, 0],
	[0, 1, 0, 0, 0],
];

dfs($map);

echo "fail\n";