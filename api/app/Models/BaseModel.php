<?php

namespace App\Models;

use App\DTOS\PageOutput;
use App\Helpers\DTOS\PageInput;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public static function findAllByPageable(PageInput $pageable): PageOutput
    {
        $data = static::offset($pageable->getOffset())
            ->limit($pageable->getLimit())
            ->get();
        $hasNextPage = false;

        if ($data->count() > $pageable->getLimit()) {
            $data = $data->slice(0, $pageable->getLimit());
            $hasNextPage = true;
        }
        
        return new PageOutput($pageable, $data, $hasNextPage);
    }
}
