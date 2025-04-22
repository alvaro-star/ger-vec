<?php

namespace App\DTOS;

use App\Helpers\Dtos\PageInput;
use Illuminate\Database\Eloquent\Collection;

/**
 * @template T
 */
class PageOutput
{
    public int $nPage;
    public int $size;
    /** @var T[] */
    public Collection $content;
    public bool $hasNextPage;

    public function __construct(PageInput $pageable, Collection $content, bool $hasNextPage)
    {
        $this->nPage = $pageable->page;
        $this->size = $pageable->size;
        $this->content = $content;
        $this->hasNextPage = $hasNextPage;
    }

    /**
     * A funcao verifica se existe uma seguinte pagina
     * com base no tamanho de uma colecao de uma requisicao
     */
    public static function generateResponseByCollection(PageInput $pageable, Collection $content)
    {
        $hasNextPage = false;
        if ($content->count() > $pageable->getLimit()) {
            $content = $content->slice(0, $pageable->getLimit());
            $hasNextPage = true;
        }
        return new PageOutput($pageable, $content, $hasNextPage);
    }
}
