<?php
namespace sf\cache;

use sf\base\Component; 
use sf\cache\CacheInterface;
use Exception;
use Redis;

class RedisCache extends Component implements CacheInterface
{
	public $redis;

	public function init() {
	 	if (is_array($this->redis)) {
			extract($this->redis);
			$redis = new Redis();
			$redis->connect($host, $port);
			if (!empty($password)) {
				$this->auth($password);
			}
			$redis->select($database);
			if (!empty($options)) {
				call_user_func_array([$redis, 'setOption'], $options);
			}	
			$this->redis = $redis;
		}	
		if (!$this->redis instanceof Redis) {
			throw new Exception('Cache::redis must be either a Redis connection instance.');
		}
	}
	
	public function buildKey($key)
	{
		if (!is_string($key)) {
			$key = json_encode($key);
		}
			return md5($key);
	}
	
	public function get($key)
	{
		$key = $this->buildKey($key);
		return $this->redis->get($key);
	}
	
	public function exits($key)
	{
		$key = $this->buildKey($key);
		return $this->redis->exits($key);
	}
	
	public function mget($keys)
	{
		$result = [];
		foreach ($keys as $key) {
			$result [] = $this->get($key);
		}
		return $result;
	}			
	
	public function set($key, $value, $duration = 0)
	{
		$key = $this->buildKey($key);
		if ($duration !== 0) {
			$expire = (int) $duration * 1000;
			return $this->redis->set($key, $value, $duration);	
		} else {
			return $this->redis->set($key, $value);
		}
	}

	public function mset($items, $duration = 0)
	{
		$failKeys = [];
		foreach ($items as $key => $val) {
			if ($this->set($key, $val, $duration) === false) {
				$failKeys[] = $key;
			}	
		}		
		return $failKeys;
	}
	
	public function add($key, $value, $duration = 0)
	{
		if (!$this->exits($key)) {
			return $this->set($key, $value, $duration);
		} else {
			return false;
		}
	}
	
	public function madd($items, $duration = 0)
	{
		$failKeys = [];
		foreach ($items as $key => $val) {
			if ($this->add($key, $val, $duration) === false) {
				$failKeys[] = $key;
			}
		}
		return $failKeys;
	}
	
	public function delete($key)
	{
		$key = $this->buildKey($key);
		return $this->redis->del($key);
	}

	public function flush()
	{
		return $this->redis->flushDb();		
	} 
}
