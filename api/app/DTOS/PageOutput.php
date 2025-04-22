<?php

namespace App\DTOS;


use Illuminate\Database\Eloquent\Collection;

/**
 * @template T
 */
class PageOutput
{
    public int $nPage;
    public int $size;

    public int $nElementos;
    public int $nPages;

    public bool $hasNextPage;

    /** @var T[] */
    public Collection $content;

    public function __construct(PageInput $pageable, Collection $content, int $nElementos)
    {
        $this->nPage = $pageable->page;
        $this->size = $pageable->size;
        $this->content = $content;
        $this->nElementos = $nElementos;

        $this->nPages = (int) ceil($nElementos / $this->size);
        $this->hasNextPage = ($this->nPage) < $this->nPages;
    }
}
