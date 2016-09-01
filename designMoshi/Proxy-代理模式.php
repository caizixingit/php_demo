<?php

/**
 * 代理模式
 *
 * 为其他对象提供一个代理以控制这个对象的访问
 *
 * proxy代理模式是一种结构型设计模式，主要解决的问题是：在直接访问对象时带来的问题，比如说：要访问的对象在远程的机器上。在面向对象系统中，有* 些对象由于某些原因（比如对象创建开销很大，或者某些操作需要安全控制，或者需要进程外的访问），直接访问会给使用者或者系统结构带来很多麻烦， * 我们可以在访问此对象时加上一个对此对象的访问层
 *
 * CSmarty, JPush, InternalHttp, DBProxy, Redis/Connection等都是代理模式
 */
interface Proxy
{
	public function request();

	public function display();
}

class RealSubject implements Proxy
{
	public function request()
	{
		echo "RealSubject request<br/>";
	}

	public function display()
	{
		echo "RealSubject display<br/>";
	}
}

class ProxySubject implements Proxy
{
	private $_subject = null;

	public function __construct()
	{
		$this->_subject = new RealSubject();
	}

	public function request()
	{
		$this->_subject->request();
	}

	public function display()
	{
		$this->_subject->display();
	}
}

$objProxy = new ProxySubject();
$objProxy->request();
$objProxy->display();