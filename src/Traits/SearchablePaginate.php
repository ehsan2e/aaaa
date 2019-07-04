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
     * @param int|null $perPage
     * @return iPaginationGenerator
     */
    public function paginate(Request $request, Builder $queryBuilder, int $perPage = null): iPaginationGenerator
    {
        return (new PaginationGenerator($request->query->all(), $queryBuilder, $perPage))
            ->rawQuery(Auth::user()->can('raw-query'));
    }
}