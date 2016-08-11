<?php
/**
* redis连接处理 ———— 一致性hash
*
* @author caizixin
* @since 20141124
*/

class LibRedis{

    private $nodeList = array();            //hash环上的虚拟结点集合
    private $keyAndRedisMapping = array();  //key与redis实例之间的映射
    private $hostAndRedisMapping = array(); //host与redis实例之间的映射
    private $redisConnectConf = array();  //reids连接配置：密码与超时时间
    private static $queueInstance = null;

    public function __construct($nodeListFile = REDIS_CACHE_NODE_LIST, $connectConf) {
        //获取redis集群配置
        if (file_exists($nodeListFile)) {
            $redisNodeList = file($nodeListFile);
            if (is_array($redisNodeList)) {
                //获取缓存集群列表
                foreach ($redisNodeList as $line) {
                    list($node, $virtualNodeNum) = explode(' ', $line);
                    for ($i = 0; $i < $virtualNodeNum; $i++) {
                        $this->nodeList[sprintf("%u", crc32($node.'-'.$i))] = $node.'-'.$i;
                    }
                }
                //排序
                ksort($this->nodeList);
            } else {
      //          Yii::log("redis_node_file_is_not_valid:" . $nodeListFile, CLogger::LEVEL_WARNING,  'fatal');
                return false;
            }
        } else {
    //        Yii::log("redis_node_file_is_not_exists:" . $nodeListFile, CLogger::LEVEL_WARNING,  'fatal');
            return false;
        }

        //redis连接配置：密码与超时时长
        $this->redisConnectConf = $connectConf;
    }

    /**
    * 根据key获取redis实例
    * @param $key
    */
    private function getRedis($key) {
        if (!isset($this->keyAndRedisMapping[$key]) || !is_object($this->keyAndRedisMapping[$key])) {
            $target = $this->lookup(sprintf("%u", crc32($key)));           
            list($host, ) = explode('-', $target);

            @$redisForHost = $this->hostAndRedisMapping[$host];
            if (is_object($redisForHost)) {
                $this->keyAndRedisMapping[$key] = $redisForHost;
                return $redisForHost;
            }

            $redis = new \Redis();
            $ret = $this->connect($host, $this->redisConnectConf['timeout'], $this->redisConnectConf['password'], $redis);
            if ($ret === false) {
			//	Yii::log("S_Redis_connect_fail_{$key}", CLogger::LEVEL_WARNING,  'fatal');
                $this->keyAndRedisMapping[$key] = false;
                $this->hostAndRedisMapping[$host] = false;
            } else {
                $this->keyAndRedisMapping[$key] = $redis;
                $this->hostAndRedisMapping[$host] = $redis; 
            }
        }

        return $this->keyAndRedisMapping[$key];
    }

    /**
    * 根据key的hash值，从hash环上查找它应该落到的结点
    * @param @resource ：sprintf("%u", crc32($key))的结果值
    */
    private function lookup($resource) {
        foreach ($this->nodeList as $key => $value) {
            if ($key > $resource) {
                return $value;
            }
        }
        //搜寻一遍没有发现可用的,取第一台
        // return array_shift($this->nodeList);
        $tmp = array_values($this->nodeList);
        return $tmp[0];
    }

    /**
     * 连接redis服务器
     * @param string $host
     * @param int $timeout
     * @param string $passwd
     */
    private function connect($host, $timeout, $passwd, $redis)
    {
        list($hostAddr, $port) = explode(':', $host);
        
        $ret = $redis->connect($hostAddr, $port, $timeout);
        if(!$ret)
        {
           // Yii::log("S_Redis_connect_fail_{$hostAddr}:{$port}", CLogger::LEVEL_ERROR,  'fatal');
            return false;
        }
        
        $ret = $redis->auth($passwd);
        if(!$ret)
        {
           // Yii::log("S_Redis_connect_fail_{$hostAddr}:{$port}:p{$passwd}", CLogger::LEVEL_ERROR,  'fatal');
            return false;
        }

		//Yii::log("S_Redis_connect_suc_{$hostAddr}:{$port}", CLogger::LEVEL_INFO,  'notice');
        return true;
    }

    /***************************************************
    *  下面是对外提供的方法
    ***************************************************/


    /**
     * 获取list的长度
     * @param string $key
     */
    public function getLSize($key) {
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->lSize($key);
            if ($ret == 0) {
                $ret = $redis->exists($key);
                $ret = $ret == false ? false : 0;
            }
        }
        return $ret;
    } 

    /**
     * 获取list中从$start到$end之间的数据
     * @param string $key
     * @param int $start
     * @param int $end
     * @return boolean|array
     */
    public function getLRange($key, $start, $end) {
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->lRange($key, $start, $end);
            if (empty($ret)) {
                $ret = $redis->exists($key);
                $ret = ($ret == false ? false : array());
            }
        }
        return $ret;
    }

	/**
	 * 设置list中的范围为从start到end
	 * @param string $key
	 * @param int $start
	 * @param int $end
	 * @return boolean
	 */
	public function setlTrim($key, $start, $end) {
		$ret = false;
		$redis = $this->getRedis($key);
		if ($redis) {
			$ret = $redis->lTrim($key, $start, $end);
			}
		return $ret;
	}

    /**
     * 
     * @param string $key
     * @param string $value
     * @param string $func   lPush | rPush | lPushx | rPushx
     */
    public function push($key, $value, $func='lPushx') {
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->$func($key, $value);
            if ($ret === false) {
                $ret = $redis->$func($key, $value);
            }
        }
        return $ret;
    }

    /**
     * 
     * @param string $key
     * @param string $func lPop| rPop
     */
    public function pop($key, $func='lPop') {
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->$func($key);
            if ($ret === false) {
                $ret = $redis->$func($key);
            }
        }
        return $ret;
    }


	public function set($key, $value)
	{
		if(is_array($value))
		{
			$value = base64_encode(serialize($value));
		}
		$ret = false;
		$redis = $this->getRedis($key);
		if($redis)
		{
			$ret = $redis->set($key, $value);
			if($ret === false)
			{
				//add log
				$ret = $redis->set($key, $value);
				//add log
			}
		}
		return $ret;
	}
    /**
     * 
     * @param string $key
     * @param string $value
     * @param int $time 缓存时间单位 s
     * @return boolean
     */
    public function setex($key, $value, $time) {
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->setex($key, $time, $value);
            if ($ret === false) {
                //Dapper_Log::warning("S_Redis_setex_fail2_{$key}", 'dal');
                $ret = $redis->setex($key, $time, $value);
                if ($ret === false) {
					//log
                }
            } 
        }
        return $ret;
    }

    /**
     * 
     * @param string $key
     * @param string $value
     * @param int $time 缓存时间单位 s
     * @return boolean
     */
    public function increment($key) {
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->incr($key);
            if ($ret === false) {
             //   Dapper_Log::warning("S_Redis_incre_fail1_{$key}", 'dal');
                $ret = $redis->incr($key);
            }
        }
        return $ret;
    }

    /**
     * 
     * @param string $key
     * @param string $value
     * @param int $count delete count
     * @return boolean
     */
    public function lRem($key, $value, $count=0) {
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->lRem($key, $value, $count);
            if ($ret === false) {
                $ret = $redis->lRem($key, $value, $count);
            }
        }
        return $ret;
    }

    /**
     * 
     * @param string $key
     */
    public function get($key) {
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->get($key);
			if($ret === false)
			{
				$ret = $redis->get($key);
			}
        }
        return $ret;
    }

    public function getArr($key) {
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->get($key);
			if($ret === false)
			{
				$ret = $redis->get($key);
			}
		}
		if(!is_array($ret))
		{
			$str = base64_decode($ret);
			$ret = unserialize($str);
			if(is_array($ret))
			{
				return $ret;
			}
			else
				return false;
		}
        return false;
    }
    /**
     * 删除key值
     * @param string $key
     * @return boolean
     */
    public function delete($key) {
        $ret = false;
		$key = strval($key);
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->del($key);
            if ($ret === false) {
                $ret = $redis->del($key);
            }
        }
        return $ret;
    }

    /**
     * 判断key是否存在 
     * @param string $key
     */
    public function exists($key) {
        $ret = -1;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->exists($key);
        }
        return $ret;
    }

    /**
     * 添加store set 数据
     * @param string $key
     * @param int $score
     * @param string $value
     */
    public function zAdd($key, $score, $value) {
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->zAdd($key, $score, $value);
            if ($ret == 0) {
                $ret = $redis->zAdd($key, $score, $value);
            }
        }
        return $ret;
    }

    /**
     * 获取store set里面的数据
     * @param string $key
     * @param int $start
     * @param int $end
     * @param int $score
     * @param string $func : 获取storeset 的数据值的函数 'zRange, zRevRange'
     * @return boolean
     */
    public function zRange($key, $start, $end, $score=false, $func='zRange') {
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->$func($key, $start, $end, $score);
            if (empty($ret)) {
                $ret = $redis->exists($key);
                $ret = ($ret === false ? false : array());
            }
        }
        return $ret;
    }

    /**
     * 删除store set中的某一个值
     * @param unknown_type $key
     * @param unknown_type $value
     * @return boolean
     */
    public function zDelete($key, $value) {
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->zDelete($key, $value);
            if ($ret === false) {
                $ret = $redis->zDelete($key, $value);
            }
        }
        return $ret;
    }

    /**
     * 获取store set中的数据量
     * @param string $key
     * @return int
     */
    public function zSize($key) {
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->zSize($key);
            if ($ret == 0) {
                $ret = $redis->exists($key);
                $ret = $ret == false ? false : 0;
            }
        }
        return $ret;
    }

    /**
     * 将对应的处理流放入队列中
     * @param string $key
     * @param string $value
     */
    public function addQueues($key, $value) {
        $ret = $this->checkQueue();
        if ($ret) {
            return self::$queueInstance->sAdd($key, $value);
        }
        return false;
    }

    /**
     * 从队列中取出处理流程
     * @param string $key
     * @return boolean|string
     */
    public function popQueue($key) {
        $ret = $this->checkQueue();
        if ($ret) {
            return self::$queueInstance->sPop($key);
        }
        return false;
    }

    /**
    * 判断队列是否可用
    */
    private function checkQueue() {
        if (!self::$queueInstance) {
            $libConfig = LibFactory::getInstance('LibConfig');
            $config = $libConfig->getConfig('queue');
            $config = $config[RUNTIME];
            
            $redis = new \Redis();
            $host = $config['hosts'][0];
            $ret = $this->connect($host, $timeout, $passwd, $redis);
            if ($ret) {
                self::$queueInstance = $redis;
            } else {
                return false;
            }
        }
        return true;
    }

	public function watch($key)
	{
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->watch($key);
		}
		return $ret;
	
	}
	/*
	 * 乐观锁，count参数表示重复次数，
	*/
	
	public function setWatch($key, $value, $count = 1)
	{
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
			if(is_array($value))
			{
				$value = base64_encode(serialize($value));
			}
            $ret = $redis->multi()->set($key, $value)->exec();
			var_dump($ret);
			if($ret === false)
			{
				if($count > 1)
					$ret = $this->setWatch($key, $value, $count - 1);
			}
		}
		return $ret;
	}

    /**
     * 设置key的过期时间
     */
    public function expire($key, $second) {
        $ret = false;
        $redis = $this->getRedis($key);
        if ($redis) {
            $ret = $redis->expire($key, $second);
            if ($ret === false) {
                $ret = $redis->expire($key, $second);
            }
        }
        return $ret;
    }

	/*
	 *   集合系列SADD,给集合添加元素
	 */
	 public function sAdd($key, $value)
	 {
		$ret = false;
		$redis = $this->getRedis($key);
		if($redis)
		{
			$ret = $redis->SADD($key, $value);
			if($ret === false)
			{
				$ret = $redis->SADD($key, $value); 
			}
		}
		return $ret;
	 }
	/*
	 *   集合系列SMEMBERS,获取集合所有数据
	 */
	 public function sMembers($key)
	 {
		$ret = false;
		$redis = $this->getRedis($key);
		if($redis)
		{
			$ret = $redis->sMembers($key);
			if($ret === false)
			{
				$ret = $redis->sMembers($key); 
			}
		}
		return $ret;
	 }
	/*
	 *   集合系列SREM,给集合添加元素
	 */
	 public function sRem($key, $value)
	 {
		$ret = false;
		$redis = $this->getRedis($key);
		if($redis)
		{
			$ret = $redis->sRem($key, $value);
			if($ret === false)
			{
				$ret = $redis->sRem($key, $value); 
			}
		}
		return $ret;
	 }
}

