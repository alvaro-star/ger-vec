<?php

namespace App\Utils\Redis\Entity;

use App\Utils\Redis\CacheData\CacheWatcher;
use App\Utils\Redis\CacheRequest;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CacheEntityRequest extends CacheRequest
{
    protected string $table;
    private ?CacheWatcher $object = null;

    public function __construct(string $table)
    {
        parent::__construct(null);
        $this->table = $table;
        $this->setKey($table);
        $this->loadFromCache();
        $this->generateObjectIfNotExists();
    }

    public function alterNElements()
    {
        $this->getObject()->setUpdatedAt(new DateTime());
        $this->getObject()->setCreatedAt(new DateTime());
        $this->saveCache();
    }
    public function updateElement()
    {
        $this->getObject()->setUpdatedAt(new DateTime());
        $this->saveCache();
    }

    public function generateObjectIfNotExists(): void
    {
        if ($this->object !== null) return;
        $this->object = new CacheWatcher($this->table);
    }

    public function setObject($object)
    {
        $this->object = $object;
    }

    public function getObject(): ?CacheWatcher
    {
        return $this->object;
    }
}
