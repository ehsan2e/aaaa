<?php

namespace App\Http\Controllers\Dashboard\Admin\Sales;

use App\Http\Controllers\Dashboard\Admin\AbstractAdminController;
use App\Payment;
use Illuminate\Database\Eloquent\Builder;
use NovaVoip\Interfaces\iPaginationGenerator;

class PaymentController extends AbstractAdminController
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
    protected $viewBasePath = 'sales.payment';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return Payment::query();
    }

    protected function getSearchableFields(): array
    {
        return [
            'id',
            'amount',
            'gateway',
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TaxGroup $taxGroup
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        return $this->renderForm('dashboard.admin.sales.payment.show', compact('payment'));
    }
}
