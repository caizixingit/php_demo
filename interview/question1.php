<?php
/**
 * Created by PhpStorm.
 * User: caizixin
 * Date: 17/1/16
 * Time: 上午10:56
 */

/**
 * 问题来源
 * https://www.zhihu.com/question/19757909 何广宇 问题1
 */

$file = "http://php.net/manual/en/question1.php";
timeDump("fetching http://php.net/manual/en/question1.php");
$content = file_get_contents($file);
timeDump("parsing start");

preg_match('/<ul class=\'parent-menu-list\'>[\s\S]*<ul class=\'child-menu-list\'>[\s\S]*<\/ul>[\s\S]*<\/ul>/U', $content, $matches);
$rightSideContent = $matches[0];
preg_match_all('/<a.*href="(.*)".*>(.*)<\/a>/U', $rightSideContent, $matches);

timeDump("the right side list is:");
if(!empty($matches[1]))
{
	$fileContent = '';
	foreach($matches[1] as $num => $href)
	{
		$title = $matches[2][$num];
		$tmp = "{$title} (http://php.net/manual/en/{$href})\n";
		$fileContent .= $tmp;
		echo $tmp;
	}

	timeDump("parsing end");
	timeDump("saving to file langref.txt");
	file_put_contents('langref.txt', $fileContent);
	timeDump("saved");
}


function timeDump($str)
{
	$time = date('Y-m-d H:i:s');
	echo "[{$time}] {$str}\n";
}