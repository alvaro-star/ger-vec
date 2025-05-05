<?php

namespace App\Models;

use App\Config\CustomBuilder;
use App\DTOS\PageInput;
use App\DTOS\PageOutput;
use App\Utils\Redis\Pageable\CachePageableRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use function PHPUnit\Framework\returnSelf;

class BaseModel extends Model
{

    public static CachePageableRequest|null $pageableCache = null;
    public static function loadCacheInfo(Request $request, array $filters, array $watchers)
    {
        static::$pageableCache = new CachePageableRequest($request, $filters, $watchers);
    }

    public function newEloquentBuilder($query)
    {

        $builder = new CustomBuilder($query);
        $builder->pageableCache = static::$pageableCache;
        return $builder;
    }

    public function uniqueKeyIsOcuped($field, $value): array
    {
        $return = static::where($field, $value)->where('id', '!=', $this->id)->exists();
        if ($return)
            return ['O novo valor não é único'];
        return [];
    }
}
