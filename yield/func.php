<?php
/**
 * Created by PhpStorm.
 * User: caizixin
 * Date: 16/11/23
 * Time: 下午6:25
 */


class Task {
	protected $taskId;
	protected $coroutine;
	protected $sendValue = null;
	protected $beforeFirstYield = true;

	public function __construct($taskId, Generator $coroutine) {
		$this->taskId = $taskId;
		$this->coroutine = stackedCoroutine($coroutine);
	}

	public function getTaskId() {
		return $this->taskId;
	}

	public function setSendValue($sendValue) {
		$this->sendValue = $sendValue;
	}

	public function run() {
		if ($this->beforeFirstYield) {
			$this->beforeFirstYield = false;
			return $this->coroutine->current();
		} else {
			$retval = $this->coroutine->send($this->sendValue);
			$this->sendValue = null;
			return $retval;
		}
	}

	public function isFinished() {
		return !$this->coroutine->valid();
	}
}

class Scheduler {
	protected $maxTaskId = 0;
	protected $taskMap = []; // taskId => task
	protected $taskQueue;
	protected $waitingForRead = [];
	protected $waitingForWrite = [];

	public function __construct() {
		$this->taskQueue = new SplQueue();
	}

	public function newTask(Generator $coroutine) {
		$tid = ++$this->maxTaskId;
		$task = new Task($tid, $coroutine);
		$this->taskMap[$tid] = $task;
		$this->schedule($task);
		return $tid;
	}


	public function schedule(Task $task) {
		$this->taskQueue->enqueue($task);
	}


	public function run() {
		//$this->newTask($this->ioPollTask());

		while (!$this->taskQueue->isEmpty()) {
			$task = $this->taskQueue->dequeue();
			$retval = $task->run();

			if ($retval instanceof SystemCall) {
				$retval($task, $this);
				continue;
			}

			if ($task->isFinished()) {
				unset($this->taskMap[$task->getTaskId()]);
			} else {
				$this->schedule($task);
			}
		}
	}
}

class CoroutineReturnValue {
	protected $value;

	public function __construct($value) {
		$this->value = $value;
	}

	public function getValue() {
		return $this->value;
	}
}



function retval($value) {
	return new CoroutineReturnValue($value);
}

function stackedCoroutine(Generator $gen) {
	$stack = new SplStack;

	for (;;) {
		$value = $gen->current();

		if ($value instanceof Generator) {
			$stack->push($gen);
			$gen = $value;
			continue;
		}

		$isReturnValue = $value instanceof CoroutineReturnValue;
		if (!$gen->valid() || $isReturnValue) {
			if ($stack->isEmpty()) {
				return;
			}

			$gen = $stack->pop();
			$gen->send($isReturnValue ? $value->getValue() : NULL);
			continue;
		}

		$gen->send(yield $gen->key() => $value);
	}
}


function echoTimes($msg, $max) {
	for ($i = 1; $i <= $max; ++$i) {
		echo "$msg iteration $i\n";
		yield;
	}
}

function add($num1, $num2)
{
	yield retval($num1 + $num2);
}

function task() {
	yield echoTimes('foo', 10); // print foo ten times
	echo "---\n";
	yield echoTimes('bar', 5); // print bar five times
	//yield; // force it to be a coroutine
	$sum = (yield add(1,2));
	echo "$sum \n";
}

$scheduler = new Scheduler;
$scheduler->newTask(task());
$scheduler->run();
