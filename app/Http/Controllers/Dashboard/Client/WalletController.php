<?php

namespace App\Http\Controllers\Dashboard\Client;


use App\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use NovaVoip\Interfaces\iPaginationGenerator;

class WalletController extends AbstarctClientController
{
    /**
     * @var string
     */
    protected $collectionName = 'transactions';

    /**
     * @var array
     */
    protected $searchableFields = ['amount', 'description'];

    /**
     * @var string
     */
    protected $viewBasePath = 'wallet';

    protected function prePaginationRender(iPaginationGenerator $paginationGenerator): iPaginationGenerator
    {
        return $paginationGenerator
            ->bindQueryParamFilter('from_date', function (Builder $query, $v) {
                $time = strtotime($v);
                if ($time) {
                    $query->where('created_at', '>=', Carbon::createFromTimestamp($time));
                }
            })
            ->bindQueryParamFilter('to_date', function (Builder $query, $v) {
                $time = strtotime($v);
                if ($time) {
                    $query->where('created_at', '<=', Carbon::createFromTimestamp($time));
                }
            });
    }

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return Transaction::query()->where('user_id', Auth::id());
    }

    protected function getSortableFields(): array
    {
        return [
            [__('Creation Date'), 'transactions.id', iPaginationGenerator::SORT_DESC],
        ];
    }
}