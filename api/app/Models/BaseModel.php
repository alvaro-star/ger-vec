<?php

namespace App\Models;

use App\Config\CustomBuilder;
use App\DTOS\PageInput;
use App\DTOS\PageOutput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use function PHPUnit\Framework\returnSelf;

class BaseModel extends Model
{
    public function newEloquentBuilder($query)
    {
        return new CustomBuilder($query);
    }
    public static function findAllByPageable(PageInput $pageable): PageOutput
    {
        $nElementos  = static::count();
        $data = static::offset($pageable->getOffset())
            ->limit($pageable->getLimit())
            ->get();
        return new PageOutput($pageable, $data, $nElementos);
    }
    public function uniqueKeyIsOcuped($field, $value): array
    {
        $return = static::where($field, $value)->where('id', '!=', $this->id)->exists();
        if ($return)
            return ['O novo valor não é único'];
        return [];
    }
}
