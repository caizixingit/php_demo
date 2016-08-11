<?php
/**
 * 数据库连接操作类
 *
 * @category	DB
 * @package		DBProxy
 * @author		zhaoshunyao <zhaoshunyao@baidu.com>
 * @version		$Revision: 2.0 $
 */
class DBProxy
{
	/**
	 * @var mysqli
	 */
	protected $mysqli = null;
	protected $dbname = '';
	protected $config = null;
	protected $lastSql = '';
	protected $lastErrno = -1;
	protected $lastErrmsg = '';
	protected $lastIP = '';
	protected $queryNum = 0;		//SQL查询次数
	protected $querySql = array();	//一次连接MYSQL查询的所有语句
	protected $queryTime = array();	//一次连接MYSQL查询的所有语句运行时间

	/**
	 * Array of DBProxy instances
	 * @var array
	 */
	public static $instances = array();

	/**
	 * Get a DBProxy instance for the specified database.
	 *
	 * @param string $database
	 */
	public static function getInstance($database)
	{
		if(empty($database))
		{
			return null;
		}

		if(!isset(self::$instances[$database]) || self::$instances[$database] == null)
		{

			//$config = Ting::$config['db'][$database][RUNTIME];

			if(!isset($config['username']))
			{
				return null;
			}

			self::$instances[$database] = null;
			$_config = array('username' => $config['username'],
					'password' => $config['password'],
					'timeout' => $config['timeout'],
					'port' => $config['port'],
					'hosts' => $config['hosts'],
					);
			$dbproxy = new DBProxy($database, $_config);
			if($dbproxy->mysqli && ($dbproxy->lastErrno == 0))
			{
				self::$instances[$database] = $dbproxy;
				return self::$instances[$database];
			}
			else
			{
				return false;
			}
		}
		else
		{
			return self::$instances[$database];
		}
	}

	/**
	 * DBProxy Constructor
	 *
	 * @param string $dbname	Database of this dbproxy instance wants to use
	 * @param array $config		Config of the dbproxy instance, as the following format:
	 * <code>
	 * array('username' => '',		// username to access dbproxy server
	 * 		 'password' => '',		// password to access dbproxy server
	 *		 'timeout' => xx,	// retry times when failed to connect dbproxy cluster
	 *		 'port' => xx,			// dbproxy server port
	 *		 'hosts' => array(ip1, ip2, ...),	// dbproxy server ips
	 *		)
	 * </code>
	 */
	public function __construct($dbname, array $config)
	{
		$this->config = $config;
		$this->dbname = $dbname;
		$this->lastSql = '';
		$this->mysqli = $this->createConnection();
	}

	/**
	 * DBProxy destructor.
	 * It will close all dbproxy connections created by current instance.
	 */
	public function __destruct()
	{
		if($this->mysqli)
		{
			mysqli_close($this->mysqli);
			$this->mysqli = null;
		}
	}

	/**
	 * Create dbproxy connection according to the config saved
	 * @return dblink resources
	 */
	protected function createConnection()
	{
		$arrHosts = $this->config['hosts'];
		if(empty($arrHosts))
		{
			return false;
		}

		$mysqli = mysqli_init();
		$intTimeoutSec = $this->config['timeout'] > 3 ? 3 : $this->config['timeout'];
		mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, $intTimeoutSec);
		mysqli_options($mysqli, 11, 1); //READ_TIMEOUT
		mysqli_options($mysqli, 12, 1); //WRITE_TIMEOUT

		$host = $arrHosts[0];

		$bolRet = @mysqli_real_connect($mysqli,$host,$this->config['username'],$this->config['password'],$this->dbname,$this->config['port']);

		if($errno = mysqli_connect_errno())
		{
			$this->lastErrno = $errno;
			$this->lastErrmsg = mysqli_connect_error();
			$this->lastIP = $host;
			return false;
		}
		else
		{
			$this->lastErrno = 0;
			$this->lastErrmsg = '';
			$this->lastIP = $host;

			mysqli_set_charset($mysqli, 'utf8');
			return $mysqli;
		}

		mysqli_close($mysqli);
		return false;
	}

	/**
	 * Whether the DBlink is workable.
	 * @return bool
	 */
	public function checkup()
	{
		if(empty($this->mysqli))
		{
			return false;
		}
		else
		{
			$errno = mysqli_errno($this->mysqli);
			if($errno == 2003 || $errno == 2006 || $errno == 2013)
			{
				//2003：Can't connect to MySQL server on 'hostname' (4,110) （此情况一般是网络超时、数据库压力过大等导致）
				//2006：MySQL server has gone away （dbproxy在重启时可能会出现此问题，sleep 状态的链接）
				//2013：Lost connection to MySQL server during query （dbproxy在重启时可能会出现此问题，正在执行query）

				mysqli_close($this->mysqli);
				$this->mysqli = $this->createConnection();
			}
			return true;
		}
	}

	/**
	 * close current connections.
	 */
	public function close()
	{
		if($this->mysqli)
		{
			mysqli_close($this->mysqli);
			$this->mysqli = null;
		}
	}

	/**
	 * Perform a query on the database
	 * @param string $strSql	The query string
	 * @return bool Returns true on success or false on failure
	 */
	public function doQuery($strSql)
	{
		if(!$this->checkup())
		{
			return false;
		}

		$startTime = microtime(TRUE);
		$this->lastSql = $strSql;
		$ret = mysqli_query($this->mysqli, $this->lastSql);
		$endTime = microtime(TRUE);

		$this->queryNum++;
		$this->querySql[] = $this->lastSql;
		$this->queryTime[] = $endTime - $startTime;
		return $ret;
	}

	/**
	 * Perform a select query on the database and retriev all the result rows
	 * @param string $strSql	The query string
	 * @return bool|array	Return result rows on success or false on failure
	 */
	public function queryAllRows($strSql)
	{
		if(!$this->checkup())
		{
			return false;
		}

		$startTime = microtime(TRUE);
		$this->lastSql = $strSql;
//$this->lastSql = "select count(1) from song_info where song_id=309769";

		$objRes = mysqli_query($this->mysqli, $this->lastSql);
		if(!$objRes)
		{
			return false;
		}

		$arrResult = array();
		while($arrTmp = mysqli_fetch_assoc($objRes))
		{
			$arrResult[] = $arrTmp;
		}
		$endTime = microtime(TRUE);

		$this->queryNum++;
		$this->querySql[] = $this->lastSql;
		$this->queryTime[] = $endTime - $startTime;

		return $arrResult;
	}

	/**
	 * Perform a select query on the database and retriev the first row in results
	 * @param string $strSql	The query string
	 * @return bool|array	Return result row on success or false on failure
	 */
	public function queryFirstRow($strSql)
	{
		if(!$this->checkup())
		{
			return false;
		}


		$startTime = microtime(TRUE);
		$this->lastSql = $strSql;
		$objRes = mysqli_query($this->mysqli, $this->lastSql);
		if(!$objRes)
		{
			return false;
		}

		$arrResult = mysqli_fetch_assoc($objRes);
		$endTime = microtime(TRUE);

		$this->queryNum++;
		$this->querySql[] = $this->lastSql;
		$this->queryTime[] = $endTime - $startTime;

		if($arrResult !== false)
		{
			if(empty($arrResult)){
				return false;
			}
			return $arrResult;
		}
		return false;
	}

	/**
	 * Perform a select query on the database and retriev the specified field value in the first row result
	 * @param string $strSql	The query string
	 * @param bool $isInt		Whether the specified field is integer type
	 * @return bool|int|string	Return field value on success or false on failure
	 */
	public function querySpecifiedField($strSql, $isInt = false)
	{
		if(!$this->checkup())
		{
			return false;
		}

		$startTime = microtime(TRUE);
		$this->lastSql = $strSql;
		$objRes = mysqli_query($this->mysqli, $this->lastSql);
		if (!$objRes)
		{
			return false;
		}

		$out = null;
		$arrResult = mysqli_fetch_row($objRes);
		if($arrResult)
		{
			if($isInt)
			{
				$out = intval($arrResult[0]);
			}
			$out = $arrResult[0];
		}
		else
		{
			if($isInt)
			{
				$out = 0;
			}
			$out = null;	
		}
		$endTime = microtime(TRUE);

		$this->queryNum++;
		$this->querySql[] = $this->lastSql;
		$this->queryTime[] = $endTime - $startTime;

		return $out;
	}

	/**
	 * Do multiple sql queries as a transaction
	 *
	 * @param array $arrSql	Array of sql queries to be executed
	 * @return bool Returns true on success or false on failure
	 */
	public function doTransaction(array $arrSql)
	{
		if(!$this->checkup())
		{
			return false;
		}

		mysqli_autocommit($this->mysqli, false);

		foreach($arrSql as $strSql)
		{
			$ret =  mysqli_query($this->mysqli, $strSql);
			if(!$ret)
			{
				$this->lastSql = $strSql;
				mysqli_rollback($this->mysqli);
				mysqli_autocommit($this->mysqli, true);
				return false;
			}
		}

		mysqli_commit($this->mysqli);
		mysqli_autocommit($this->mysqli, true);

		$this->queryNum++;
		$this->querySql[] = $this->lastSql;

		return true;
	}

	/**
	 * Selects the defaut database for database queries
	 * @param string $database	The database name
	 * @return bool Returns true on success or false on failure
	 */
	public function selectDB($dbname)
	{
		if(!$this->checkup())
		{
			return false;
		}
		return mysqli_select_db($this->mysqli, $dbname);
	}

	/**
	 * Get the last inserted data's autoincrement id
	 * @return int
	 */
	public function getLastInsertID()
	{
		if(!$this->mysqli)
		{
			return false;
		}
		return mysqli_insert_id($this->mysqli);
	}

	/**
	 * Get number of affected rows of the last SQL query
	 * @return int
	 */
	public function getAffectedRows()
	{
		if(!$this->mysqli)
		{
			return false;
		}
		return mysqli_affected_rows($this->mysqli);
	}

	/**
	 * Escapes special characters in a string for use in a SQL query
	 * @param string $str	String to be escaped
	 * @return bool|string	Return escaped string on success or false on failure
	 */
	public function realEscapeString($str)
	{
		if(!$this->mysqli)
		{
			return false;
		}
		return mysqli_real_escape_string($this->mysqli, $str);

	}

	/**
	 * Get errno
	 */
	public function getErrno()
	{
		if($this->mysqli)
		{
			return mysqli_errno($this->mysqli);
		}
		else
		{
			return -1;
		}
	}

	/**
	 * Get errmsg
	 */
	public function getErrmsg()
	{
		if($this->mysqli)
		{
			return mysqli_error($this->mysqli);
		}
		else
		{
			return 'mysql server not available';
		}
	}

	/**
	 * 得到最后查询的SQL
	 */
	public function getSqlStr()
	{
		return $this->lastSql;
	}

	/**
	 * 得到查询次数
	 */
	public function getQueryNum()
	{
		return $this->queryNum;
	}

	/**
	 * 得到全部查询的SQL
	 */
	public function getQuerySql()
	{
		return $this->querySql;
	}

	/**
	 * 得到查询的SQL执行时间
	 */
	public function getQueryTime()
	{
		return $this->queryTime;
	}

	public static function getDebugInfo(){
		$info = array();
		foreach (self::$instances as $index => $one){
			$info[$index]['sql'] = $one->getQuerySql();
			$info[$index]['time'] = $one->getQueryTime();
			$info[$index]['num'] = $one->getQueryNum();
		}
		return $info;
	}

}
?>
