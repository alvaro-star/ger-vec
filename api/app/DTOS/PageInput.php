<?php


namespace App\DTOS;

use Illuminate\Http\Request;

const PAGE_DEFAULT = 1;
const SIZE_DEFAULT = 10;

class PageInput
{
    public int $page;
    public int $size;

    public function __construct(Request $request)
    {
        $this->page  = $this->getDefaultIntValue($request->query('page'), PAGE_DEFAULT);
        $this->size  = $this->getDefaultIntValue($request->query('size'), SIZE_DEFAULT);
    }

    private function getDefaultIntValue(?string $value, int $default): int
    {
        $int_value = intval($value);
        if ($int_value == 0) $int_value = $default;
        return $int_value;
    }

    public function getOffset()
    {
        return ($this->page - 1) * $this->size;
    }

    public function getLimit()
    {
        return $this->size;
    }
}
