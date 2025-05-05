<?php

namespace App\Utils\Redis\Pageable;

use App\Utils\Redis\CacheRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CachePageableRequest extends CacheRequest
{
    private array $filters;
    private CachePageInfo|null $object;
    private array $watchers;

    public function __construct(Request $request, array $filtersName, array $watchers)
    {
        parent::__construct($request);
        $this->filters = $this->selectFiltersUsed($filtersName, $request);

        $key = $this->generateRedisKey();
        $this->setKey($key);

        $this->watchers = $watchers;
        $this->object = null;


        $this->loadFromCache();
    }

    private function selectFiltersUsed(array $filtersName, Request $request)
    {
        $filtersUsed = [];
        foreach ($filtersName as $filter) {
            $filterValue = $request->query($filter) ?? null;
            $filterValue = strtoupper($filterValue);
            if ($filterValue != null)
                $filtersUsed[$filter] =  $filterValue;
        }
        return $filtersUsed;
    }

    private function generateRedisKey(): string
    {
        $filters_key = array_keys($this->filters);
        sort($filters_key);
        $key = $this->getKey();
        foreach ($filters_key as $filter) {
            $filterKeyValue = $filter . "=" . $this->filters[$filter];
            $key = $key . $filterKeyValue;
        }
        return $key;
    }

    public function generateObjectIfNotExists()
    {
        if ($this->object  != null) return;
        $object = new CachePageInfo();
        $object->setWatchers($this->watchers);
        $this->setObject($object);
    }

    public function isCacheValid()
    {
        $object = $this->getObject();

        if ($object == null) return false;

        // Isto para determinar se se realiza uma busca por atributos
        $isQueryUsed = array_key_exists('query', $this->filters);

        foreach ($object->getWatchers() as $watcherName => $watcherCache) {

            $watcherRedisSerialized = Redis::get($watcherName);

            if ($watcherRedisSerialized ==  null) return false;

            $watcherRedis = unserialize($watcherRedisSerialized);

            if ($isQueryUsed) {
                if ($watcherCache->getUpdatedAt() < $watcherRedis->getUpdatedAt())
                    return false;
            } else if ($watcherCache->getCreatedAt() < $watcherRedis->getCreatedAt())
                return false;
        }
        return true;
    }

    public function setObject($object)
    {
        $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }
}
