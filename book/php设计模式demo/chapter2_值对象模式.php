<?php

/**
 * Created by PhpStorm.
 * User: caizixin
 * Date: 16/9/8
 * Time: 上午11:33
 */
class BadDollar
{
	protected $amount;

	public function __construct($amount = 0)
	{
		$this->amount = (float)$amount;
	}

	public function getAmount()
	{
		return $this->amount;
	}

	public function add($dollar)
	{
		$this->amount += $dollar->getAmount();
	}
}

class Dollar
{
	protected $amount;

	public function __construct($amount = 0)
	{
		$this->amount = (float)$amount;
	}

	public function getAmount()
	{
		return $this->amount;
	}

	public function add($dollar)
	{
		return new Dollar($this->amount + $dollar->getAmount());
	}
}


class Work
{
	protected $salary;

	public function __construct()
	{
		$this->salary = new BadDollar(200);
	}

	public function payDay()
	{
		return $this->salary;
	}
}

class Person
{
	public $wallet;
}

function testBadDollarWorking()
{
	$job = new Work;
	$p1 = new Person;
	$p2 = new Person;
	$p1->wallet = $job->payDay();
	$this->assertEqual(200, $p1->wallet->getAmount());
	$p2->wallet = $job->payDay();
	$this->assertEqual(200, $p2->wallet->getAmount());
	$p1->wallet->add($job->payDay());
	$this->assertEqual(400, $p1->wallet->getAmount()); //this is bad — actually 400
	$this->assertEqual(200, $p2->wallet->getAmount()); //this is really bad — actually 400
	$this->assertEqual(200, $job->payDay()->getAmount());
}

testBadDollarWorking();