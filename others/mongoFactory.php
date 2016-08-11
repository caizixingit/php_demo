<?php

class MongoProxy
{
	private static $instances;
	public static function getInstance($database)
	{
		if(empty($database))
		{
			return null;
		}

		if(isset(self::$instances[$database]) && self::$instances[$database] != null)
		{
			return self::$instances[$database];
		}

		$mongo = new MongoProxy($database);

		self::$instances[$database] = $mongo;
		return self::$instances[$database];
	}

	public function __construct($database)
	{
		$config = array(
					'test' => array(
						'hosts' => array('127.0.0.1'),
						'port' => '27017',
						'user' => 'test',
						'password' => 'test123'
						)
				);

		$this->database = $database;
		$this->conf = $config[$database];
		$this->createConnection();
	}

	private function createConnection()
	{
		$hosts = $this->conf['hosts'];
		if(empty($hosts) || !is_array($hosts))	
		{
			return false;
		}

		shuffle($hosts);
		$hosts = array_merge($hosts, $hosts);

		foreach($hosts as $host)
		{
			$conn = new Mongo('mongodb://test:test123@127.0.0.1:27017/test');
			$db = $conn->selectDB($this->database);
			if($db)
			{
				$this->db = $db;
				return true;
			}
			//log
		}
		//log
		return false;
	}

	public function insert($table, $data)
	{
		$result = $this->db->table->insert($data, true);
		return $result;
	}
}
