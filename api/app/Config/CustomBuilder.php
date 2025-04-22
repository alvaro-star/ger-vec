<?php

namespace App\Config;

use Illuminate\Database\Eloquent\Builder;
use App\DTOS\PageInput;
use App\DTOS\PageOutput;

class CustomBuilder extends Builder
{
    public function getByPageable(PageInput $pageable): PageOutput
    {
        $clonedQuery = clone $this;

        $data = $this->limit($pageable->getLimit())
            ->offset($pageable->getOffset())
            ->get();

        // Segunda operação: obter os dados sem o limite + 1
        $nElementos = $clonedQuery->count();

        return new PageOutput($pageable, $data, $nElementos);
    }
}
