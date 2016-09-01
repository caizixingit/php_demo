<?php
//创建可抛出一个异常的函数
function checkNum($number)
{
	if($number>1)
	{
		throw new Exception("Value must be 1 or below");
	}
	return true;
}

//在 "try" 代码块中触发异常
	checkNum(2);
	//If the exception is thrown, this text will not be shown
	echo 'If you see this, the number is 1 or below';

?>
