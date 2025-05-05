<?php

namespace App\Config;

use Illuminate\Database\Eloquent\Builder;
use App\DTOS\PageInput;
use App\DTOS\PageOutput;
use App\Utils\Redis\Pageable\CachePageableRequest;

class CustomBuilder extends Builder
{
    public CachePageableRequest|null $pageableCache = null;

    public function getByPageable(PageInput $pageable): PageOutput
    {

        $clonedQuery = clone $this;

        $data = $this->limit($pageable->getLimit())
            ->offset($pageable->getOffset())
            ->get();

        $nElementos = 0;
        if ($this->pageableCache != null) {
            if (!$this->pageableCache->isCacheValid()) {
                $nElementos  = $clonedQuery->count();
                $this->pageableCache->generateObjectIfNotExists();
                $this->pageableCache->getObject()->updateData($nElementos);
                $this->pageableCache->saveCache();
            } else {
                $nElementos = $this->pageableCache->getObject()->getTotalElements();
            }
        } else {
            $nElementos  = $clonedQuery->count();
        }


        return new PageOutput($pageable, $data, $nElementos);
    }
}
