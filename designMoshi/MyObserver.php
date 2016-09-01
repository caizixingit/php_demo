<?php
/**
 * 观察者模式
 *
 * 定义对象间的一种一对多的依赖关系,以便当一个对象的状态发生改变时,所有依赖于它的对象都得到通知并自动刷新
 * 能够便利地创建查看目标对象状态的对象,并且提供与核心对象非耦合的指定功能
 * 插件系统
 *
 * 想到的一个应用场景：支付完成回调接口通常会执行若干步操作，可以将若干个操作定义为Observer，统一用一个Subject进行回调触发控制
 */
class CPush implements SplSubject
{
	private $_observers = [];
	private $content;

	public function attach(SplObserver $observer)
	{
		$this->_observers[] = $observer;
	}

	public function detach(SplObserver $observer)
	{
		$key = array_search($observer, $this->_observers);

		if($key !== false)
		{
			unset($this->_observers[$key]);
		}
	}

	public function notify()
	{
		foreach($this->_observers as $observer)
		{
			$observer->update($this);
		}
	}

	public function sendPush($content)
	{
		$this->content = $content;
		$this->notify();
	}

	public function __call($name, $arguments)
	{
		if(strpos($name, 'get') === 0)
		{
			$property = substr($name, 3);
			$property = lcfirst($property);
			return $this->$property;
		}
	}
}

class Android implements SplObserver
{
	public function update(SplSubject $push)
	{
		echo "Android push {$push->getContent()}\n";
	}
}

class IOS implements SplObserver
{
	public function update(SplSubject $push)
	{
		echo "IOS push {$push->getContent()}\n";
	}
}

$push = new CPush();
$android = new Android();
$ios = new IOS();
$push->attach($android);
$push->attach($ios);

$push->sendPush('直播开启，速速围观');

$push->detach($android);

$push->sendPush('新产品上架！@#￥%……R&T*Y()M<>,.sd');