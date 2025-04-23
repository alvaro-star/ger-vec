<?php


namespace App\DTOS;

use App\Utils\TransformData;
use Illuminate\Http\Request;

const PAGE_DEFAULT = 1;
const SIZE_DEFAULT = 10;

class PageInput
{
    public int $page;
    public int $size;

    public function __construct(Request $request)
    {
        $this->page  = TransformData::stringInteger($request->query('page'), PAGE_DEFAULT);
        $this->size  = TransformData::stringInteger($request->query('size'), SIZE_DEFAULT);
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
