<?php

/**
 * Created by PhpStorm.
 * User: caizixin
 * Date: 16/9/8
 * Time: 上午11:27
 */
class CartLine
{
	public $price = 0;
	public $qty = 0;

	public function total()
	{
		return $this->price * $this->qty;
	}
}

class Cart
{
	protected $lines = [];

	public function addLine($line)
	{
		$this->lines[] = $line;
	}

	public function calcTotal()
	{
		$total = 0;
		foreach ($this->lines as $line)
		{
			$total += $line->total();
		}
		$total += $this->calcSalesTax($total);

		return $total;
	}

	protected function calcSalesTax($amount)
	{
		return $amount * 0.07;
	}
}