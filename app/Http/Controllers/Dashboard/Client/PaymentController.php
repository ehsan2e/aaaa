<?php

namespace App\Http\Controllers\Dashboard\Client;

use App\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use NovaVoip\Interfaces\iPaginationGenerator;

class PaymentController extends AbstarctClientController
{
    /**
     * @var string
     */
    protected $collectionName = 'payments';

    /**
     * we need custom filter
     * @var array
     */
    //protected $searchableFields = [];

    /**
     * @var string
     */
    protected $viewBasePath = 'payment';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return Payment::query()
            ->where('user_id', Auth::id());
    }

    protected function getSearchableFields(): array
    {
        return [
            'amount',
            'reference_number',
            function (Builder $query, string $q) {
                if (($orderId = Payment::decryptMask($q)) !== null) {
                    $query->where('payments.id', $orderId);
                }
            }
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            [__('Date'), 'payments.created_at', iPaginationGenerator::SORT_DESC],
        ];
    }


    protected function prePaginationRender(iPaginationGenerator $paginationGenerator): iPaginationGenerator
    {
        return $paginationGenerator->bindQueryParamFilter('status', 'payments.status');
    }
}
