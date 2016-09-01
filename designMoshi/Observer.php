<?php

/**
 * 观察者模式
 *
 * 定义对象间的一种一对多的依赖关系,以便当一个对象的状态发生改变时,所有依赖于它的对象都得到通知并自动刷新
 * 能够便利地创建查看目标对象状态的对象,并且提供与核心对象非耦合的指定功能
 * 插件系统
 */
class Observerable implements SplSubject
{
	private $_observers = [];

	public function attach(SplObserver $observer)
	{
		$this->_observers[] = $observer;
	}

	public function detach(SplObserver $observer)
	{
		$key = array_search($observer, $this->_observers);
		if (!($key === false))
		{
			unset($this->_observers[$key]);
		}
	}

	public function notify()
	{
		foreach ($this->_observers as $observer)
		{
			if ($observer instanceof SplObserver) $observer->update($this);
		}
	}
}

interface Observer
{
	public function update($observer);
}

interface DisplayElement
{
public function display();
}

// -- 实例类定义
class NewsObserverable extends Observerable
{
	private $_sports_news;

	public function setSportsNews($data)
	{
		$this->_sports_news = $data;
		$this->notify();
	}

	public function getSportsNews()
	{
		return $this->_sports_news;
	}

	private $_local_news;

	public function setLocalNews($data)
	{
		$this->_local_news = $data;
		$this->notify();
	}

	public function getLocalNews()
	{
		return $this->_local_news;
	}
}

class SportsNews implements SplObserver, DisplayElement
{
	private $_data = null;

	public function update(SplSubject $observer)
	{
		if ($this->_data != $observer->getSportsNews())
		{
			$this->_data = $observer->getSportsNews();
			$this->display();
		}
	}

	public function display()
	{
		echo $this->_data . date("Y-m-d H:i:s") . "<br/>";
	}
}

class LocalNews implements SplObserver, DisplayElement
{
	private $_data = null;

	public function update(SplSubject $observer)
	{
		if ($this->_data != $observer->getLocalNews())
		{
			$this->_data = $observer->getLocalNews();
			$this->display();
		}
	}

	public function display()
	{
		echo $this->_data . date("Y-m-d H:i:s") . "<br/>";
	}
}

// -- 实例化 ---

$objObserver = new NewsObserverable();
$local = new LocalNews();
$sports = new SportsNews();

$objObserver->attach($local);
$objObserver->attach($sports);

$objObserver->setSportsNews("sports news 1 ");
$objObserver->setLocalNews("local news 1 ");
$objObserver->detach($sports);
$objObserver->setLocalNews("local news 2 ");
$objObserver->setSportsNews("sports news 2 ");
$objObserver->detach($local);
$objObserver->setLocalNews("local news 3 ");