<?php

function getParam($param, $key)
{
	$list = explode('.', $key);
	$tmp = $list;
	foreach($list as $one)
	{
		if(!isset($param[$one]))
		{
			break;
		}


	}
}

