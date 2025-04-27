<?php

namespace App\DTOS;

use App\Utils\TransformData;
use Illuminate\Http\Request;

const PAGE_DEFAULT = 1;
const SIZE_DEFAULT = 10;
const SORT_DEFAULT = '';
const DIRECTION_DEFAULT = 'asc';
const FILTER_DEFAULT = '';

class PageInput
{
    public int $page;
    public int $size;
    public string $sort;
    public string $direction;
    public string $filter;
    public string $query;

    public function __construct(Request $request)
    {
        $this->page  = TransformData::stringInteger($request->query('page'), PAGE_DEFAULT);
        $this->size  = TransformData::stringInteger($request->query('size'), SIZE_DEFAULT);
        $this->sort = $request->query('sort', SORT_DEFAULT);
        $this->query = $request->query('query') ?? '';

        $asc = TransformData::stringBoolean($request->query('asc'), true);
        $this->direction = $asc ? 'asc' : 'desc';
        $this->filter = $request->query('filter', FILTER_DEFAULT);
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->size;
    }

    public function getLimit(): int
    {
        return $this->size;
    }

    public function getSort(): string
    {
        return $this->sort;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function getFilter(): string
    {
        return $this->filter;
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}
