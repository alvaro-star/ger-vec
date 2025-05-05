<?php

namespace App\Utils\Redis\Pageable;

use App\Utils\Redis\CacheData\CacheWatcher;
use DateTime;

class CachePageInfo
{
    private array $watchers;
    private int $totalElements;

    public function __construct()
    {
        $this->watchers = [];
        $this->totalElements = -1;
    }

    public function setWatchers(array $watchersName): void
    {
        foreach ($watchersName as $watcherName) {
            if (array_key_exists($watcherName, $this->watchers)) {
                continue;
            }

            $cacheWatcher = new CacheWatcher($watcherName);
            $this->watchers[$watcherName] = $cacheWatcher;
        }
    }

    public function updateData(int $totalElements)
    {
        $this->totalElements  = $totalElements;
        foreach ($this->getWatchers() as $watcherCache) {
            $watcherCache->setUpdatedAt(new DateTime());
            $watcherCache->setCreatedAt(new DateTime());
        }
    }

    /**
     * @return array<string, CacheWatcher>
     */
    public function getWatchers(): array
    {
        return $this->watchers;
    }

    public function getTotalElements(): int
    {
        return $this->totalElements;
    }
}
