<?php
class Test
{
	public $a;
	public function __construct($a)
	{
		$this->a = $a;
	}
}

$a = new Test(1);
$b = new Test(2);
$c = new Test(3);

$arr = [$a, $b, $c];

var_dump(array_search($b, $arr));


$a = [1, 2];
$b = [3, 4];
$c = [1, 2];
$arr = [$a, $b, $c];

var_dump(array_search([3,4], $arr));


$a = function ()
{
	return 1;
};
echo $a();

$data = [
	1 => '是吧'
];

echo json_encode($data, JSON_UNESCAPED_UNICODE);