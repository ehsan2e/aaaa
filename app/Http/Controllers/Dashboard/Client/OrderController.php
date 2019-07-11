<?php

namespace App\Http\Controllers\Dashboard\Client;

use App\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use NovaVoip\Interfaces\iPaginationGenerator;

class OrderController extends AbstarctClientController
{
    /**
     * @var string
     */
    protected $collectionName = 'orders';

    /**
     * we need custom filter
     * @var array
     */
    //protected $searchableFields = [];

    /**
     * @var string
     */
    protected $viewBasePath = 'order';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return Order::query()
            ->where('user_id', Auth::id());
    }


    protected function getSearchableFields(): array
    {
        return [
            function (Builder $query, string $q) {
                if (($orderId = Order::decryptMask($q)) !== null) {
                    $query->where('orders.id', $orderId);
                }
            }
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            [__('Creation Date'), 'orders.created_at', iPaginationGenerator::SORT_DESC],
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order $order
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['items.productType.category', 'country', 'province']);
        $extended = config('nova.extended_invoice') ?? config('app.debug');
        return view('dashboard.client.order.show', compact('extended', 'order'));
    }
}
