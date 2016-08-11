<?php
$content = file_get_contents('user.txt');
$userList = explode("\n", $content);

foreach($userList as $user)
{
	if(empty($user))
	{
		continue;
	}
	list($name, $account) = explode("\t", $user);

	$tmp = [];
	$tmp['name'] = $name;
	$tmp['account'] = $account;

	$result[] = $tmp;
}

file_put_contents("user.php", "<?php\n $accountList = ".var_export($result, true). ";\n");

