<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


trait PageableTrait
{
    public function paginate(Request $request, Builder $builder, string|array $orderBy = null, $sortType = 'desc'): LengthAwarePaginator
    {
        $perPage = $request->query('per_page', $request->query('por_pagina', 10));
        $page = $request->query('page', $request->query('pagina', 1));
        $builder = $orderBy ? $builder->orderBy($orderBy, $sortType) : $builder;
        //TODO: remove this attribute from the response
        //first_page_url
        //last_page_url
        //links
        //next_page_url
        //path
        //prev_page_url
        return $builder->paginate($perPage, ['*'], 'page', $page);
    }
}
