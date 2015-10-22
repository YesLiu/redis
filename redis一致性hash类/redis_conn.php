<?php
include './redis_conf.php';
class RedisHash{
	//虚拟节点数  保证一致性hash的平衡行 
	private $_virtualNode = 6;
	//存放排序后的hash环
	private $_circleHash = array();
	//redis 连接句柄
	private $_conn;
	public static $_instance;
	
	private function __construct($redis_conf)
	{
		$this->addServer($redis_conf);
	}
	
	private function __clone()
	{
		
	}
	
	static function getInstance($redis_conf)
	{
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self($redis_conf);
		}
		return self::$_instance;
	}	
	/**
	 * 将配置中的redis 加入hash环
	 * @param unknown $redis_conf 配置文件
	 * @return array: 排序后的hash环
	 */
	private function addServer($redis_conf)
	{
		for($i=0; $i<$this->_virtualNode; $i++)
		{
			foreach ($redis_conf as $v)
			{
				$_virtualKey = $this->hash($v['host'].$v['port'].$i);
				$this->_circleHash[$_virtualKey] = $v;
			}
		}
		ksort($this->_circleHash,SORT_STRING);
		return $this->_circleHash;
	}
	/**
	 * 
	 * @param unknown $key
	 * @return redis 连接句柄
	 */
	private function getRedisHandle($key)
	{
		$hostInfo = $this->getRedisHost($key);
		$this->redisConnect($hostInfo['host'],$hostInfo['port']);
		if(!$this->_conn)
			die('reids connect failed,host:'.$hostInfo['host'].'port:'.$hostInfo['port']);
		return $this->_conn;
	}
	/**
	 * 根据要存入的key 选择hash环中最近的redis
	 * @param string $key 要存入redis的key
	 * @return array: array('host'=>127.0.0.1,'port'=>6379)
	 */
	private function getRedisHost($key)
	{
		$redisKey = $this->hash($key);
		foreach ($this->_circleHash as $k=>$v)
		{
			if($k > $redisKey)
			{
				return $v;
			}  
		}
		$hostInfo = array_values(array_slice($this->_circleHash,0,1));
		return $hostInfo[0];
	}
	/**
	 * 连接redis
	 * @param unknown $host
	 * @param unknown $port
	 */
	private function redisConnect($host,$port)
	{
		$this->_conn = new redis();
		$this->_conn->connect($host,$port);
		return $this->_conn;
	}
	
	private function hash($str)
	{
		return MD5($str);
	}
	/*****************************以下为redis常用方法***********************************/
	
	function set($key,$value)
	{
		$this->_conn = $this->getRedisHandle($key);
		return $this->_conn->set($key,$value);
	}
	
	function get($key)
	{
		$this->_conn = $this->getRedisHandle($key);
		return $this->_conn->get($key);
	}
	
}

//使用
$start = microtime(true);
$redis = RedisHash::getInstance($redis);
for($i=0;$i<100000;$i++)
{
	$redis->set($i.'name','Hydra');
}
$end = microtime(true) -$start;
echo '存入10w次redis 耗时:'.$end;
