<?php

namespace RedisPubSub;


use RedisPubSub\Client\RedisClient;
use RedisPubSub\Exception\RedisException;

class RedisPubSub
{
    public function __call($name, $params)
    {
        try {
            return call_user_func_array([$this->redis, $name], $params);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            throw new RedisException($e->getMessage());
        }
    }

    /**
     * 发布消息
     * @param string $channel
     * @param string $message
     */
    public static function publish($channel, $message)
    {
        /* @var $redis \Redis */
        $redis = RedisClient::getInstance();
        $redis->publish($channel, $message);
    }

    /**
     * 订阅消息
     * @param array $channelArr
     * @param array $callback
     */
    public static function subscribe($channelArr = [], $callback)
    {
        /* @var $redis \Redis */
        $redis = RedisClient::getInstance();
        $redis->subscribe($channelArr, $callback);
    }
}