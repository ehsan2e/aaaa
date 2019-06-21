<?php

namespace NovaVoip\Traits;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NovaVoip\Helpers\PaginationGenerator;
use NovaVoip\Interfaces\iPaginationGenerator;

trait SearchablePaginate
{
    /**
     * @param Request $request
     * @param Builder $queryBuilder
     * @return iPaginationGenerator
     */
    public function paginate(Request $request, Builder $queryBuilder): iPaginationGenerator
    {
        return (new PaginationGenerator($request->query->all(), $queryBuilder))
            ->rawQuery(Auth::user()->can('raw-query'));
    }
}