<?php

namespace App\Utils\Redis\CacheData;

use DateTime;

class CacheWatcher
{
    private string $nome;
    private DateTime $updatedAt;
    private DateTime $createdAt;


    public function __construct($nome)
    {
        $this->nome =  $nome;
        $this->updatedAt =  new DateTime();
        $this->createdAt =  new DateTime();
    }
    public function getNome(): string
    {
        return $this->nome;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
