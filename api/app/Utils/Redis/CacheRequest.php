<?php

namespace App\Utils\Redis;

use Illuminate\Http\Request;
use App\Utils\Redis\CacheData\CacheWatcher;
use Illuminate\Support\Facades\Redis;

abstract class CacheRequest
{
    protected ?string $key;
    private mixed $object;

    public function __construct(?Request $request)
    {
        if ($request !== null)
            $this->key =  $request->getPathInfo();
        else
            $this->key = null;
        $this->object = null;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key)
    {
        $this->key = $key;
    }

    public function setObject($object)
    {
        return $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function loadFromCache()
    {
        $key = $this->getKey();
        $object = Redis::get($key);

        if (!is_string($object) || strpos($object, 'O:') !== 0) {
            return;
        }

        $unserialized = unserialize($object);

        $this->setObject($unserialized);
    }


    public function saveCache()
    {
        $key  = $this->key;
        $value = serialize($this->getObject());
        Redis::set($key, $value);
    }
}
