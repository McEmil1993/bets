<?php
namespace App\Util;
use CodeIgniter\Log\Logger;

class accessLogRedis {
    private $logger;
    
    private $host = '127.0.0.1';
    private $port = 16389;
    private $password = '';
    private $database = 1;
    private $expire = 300;
    
    public function __construct($host, $port, $password, $database, $expire, $logger) {
        $this->logger = $logger;
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->database = $database;
        $this->expire = $expire;
    }

    public function connect() {
        /*if (empty($this->savePath))
        {
                return false;
        }*/

        $redis = new \Redis();

        if (! $redis->connect($this->host, $this->port))
        {
                $this->logger->error('Session: Unable to connect to Redis with the configured settings.');
        }
        /*elseif (isset($this->password) && ! $redis->auth($this->password))
        {
                $this->logger->error('Session: Unable to authenticate to Redis instance.');
        }*/
        elseif (isset($this->database) && ! $redis->select($this->database))
        {
                $this->logger->error('Session: Unable to select Redis database with index ' . $this->database);
        }
        else
        {
                $this->redis = $redis;
                return true;
        }
        
        return false;
    }
    
    public function close() {
        if (isset($this->redis))
        {
            try
            {
                if (! $this->redis->close())
                {
                        return false;
                }
            }
            catch (\RedisException $e)
            {
                $this->logger->error('Session: Got RedisException on close(): ' . $e->getMessage());
            }
            $this->redis = null;
            return true;
        }

        return true;
    }
    
    public function get($key) {
        return $this->redis->get($key);
    }
    
    public function set($key, $value) {
        $this->redis->set($key, $value);
        return $this->redis->expire($key, $this->expire);
    }
    
    public function del($key) {
        $this->redis->del($key);
    }
    
    public function lpush($key, $value) {
        $this->redis->lpush($key, $value);
        return;
        //return $this->redis->expire($key, $this->expire);
    }
    
    public function rpush($key, $value) {
        $this->redis->rpush($key, $value);
        return;
        //return $this->redis->expire($key, $this->expire);
    }
    
    // data size
    public function llen($key) {
        return $this->redis->llen($key);
    }
    
    public function lpop($key) {
        $this->redis->lpop($key);
        return;
    }
    
    public function rpop($key) {
        $this->redis->rpop($key);
        return;
    }
    
    public function exists($key) {
        $result = $this->redis->exists($key);
    }
}