<?php

namespace RedisPubSub\lib\clients;

/**
 * Redis客户端
 * Class RedisClient
 * @package RedisPubSub
 * @author Hollis Ho
 */
class RedisClient
{
    private $_client;

    /**
     * 缓存连接参数
     * @var integer
     * @access protected
     */
    private $options = [];
    /**
     * RedisClient constructor.
     * @param array $options
     * @throws \RedisException
     */
    public function __construct($options = array())
    {
        if (!extension_loaded('redis')) {
            throw new \RedisException('not support redis');
        }
        $options = array_merge([
            'host'       => $options['host'] ?: '127.0.0.1',
            'port'       => $options['port'] ?: 6379,
            'timeout'    => $options['timeout'] ?: false,
            'persistent' => $options['persistent'] ?: false,
        ], $options);

        $this->options           = $options;
        $this->options['expire'] = isset($options['expire']) ? $options['expire'] : 0;
        $this->options['prefix'] = isset($options['prefix']) ? $options['prefix'] : '';
        $this->options['length'] = isset($options['length']) ? $options['length'] : 0;
        $func                    = $options['persistent'] ? 'pconnect' : 'connect';
        $this->_client           = new \Redis; false === $options['timeout'] ?
        $this->_client->$func($options['host'], $options['port']) :
        $this->_client->$func($options['host'], $options['port'], $options['timeout']);
        $options['auth'] && $this->_client->auth($options['auth']);
        $options['database'] && $this->_client->select($options['database']);
    }

    /**
     * 取得Redis实例
     * @author Hollis Ho <he_wenzhi@126.com>
     * @param array $options
     * @return mixed
     */
    public static function getInstance($options = [])
    {
        static $_instance = [];
        $guid = 'redis' . self::toGuidString($options);
        if (!isset($_instance[$guid])) {
            $obj = new RedisClient($options);
            $_instance[$guid] = $obj;
        }
        return $_instance[$guid]->_client;
    }

    /**
     * 根据PHP各种类型变量生成唯一标识号
     * @param mixed $mix 变量
     * @return string
     */
    private static function toGuidString($mix)
    {
        if (is_object($mix)) {
            return spl_object_hash($mix);
        } elseif (is_resource($mix)) {
            $mix = get_resource_type($mix) . strval($mix);
        } else {
            $mix = serialize($mix);
        }
        return md5($mix);
    }
}
