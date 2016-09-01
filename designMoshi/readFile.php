<?php

$file = 'accountList.txt';
$content = file_get_contents($file);
$list = explode("\n", $content);


foreach($list as $one)
{
	$one = trim($one);
	if(empty($one))
		continue;

	$user = explode("\t", $one);

	$tmp = [];
	$account = $user[0];
	$name = $user[1];
	$endTime = $user[2];

	$tmp['account'] = $account;
	$tmp['name'] = $name;
	$tmp['endTime'] = $endTime;


	$result[] = $tmp;
}

file_put_contents("accountList.php", "<?php\n\$accountList = ". var_export($result, true). ";\n");
