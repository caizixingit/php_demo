<?php

/**
 * Class Base
 * 里氏替换原则
 * 英文缩写：LSP (Liskov Substitution Principle)。

　　严格的定义：如果对每一个类型为T1的对象o1，都有类型为T2的对象o2，使得以T1定义的所有程序P在所有的对象o1都换成o2时，程序P的行为没有变化，那么类型T2是类型T1的子类型。

　　通俗的定义：所有引用基类的地方必须能透明地使用其子类的对象。

　　更通俗的定义：子类可以扩展父类的功能，但不能改变父类原有的功能。
 */

class Base
{
	public function getName()
	{
		echo 'base';
	}
}

class TestA extends Base
{
	public function getName()
	{
		echo 'testA';
	}
}

class TestB extends Base
{
	public function getName()
	{
		echo 'testB';
	}
}


function test(Base $a)
{
	$a->getName();
}


$a = new testA();
test($a);
