<?php

namespace App\Http\Controllers\Dashboard\Client;

use App\Invoice;
use App\Providers\PaymentGatewayProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use NovaVoip\Helpers\PaymentGatewayResolver;
use NovaVoip\Interfaces\iPaginationGenerator;
use NovaVoip\Interfaces\iPaymentGateway;

class InvoiceController extends AbstarctClientController
{
    /**
     * @var string
     */
    protected $collectionName = 'invoices';

    /**
     * we need custom filter
     * @var array
     */
    //protected $searchableFields = [];

    /**
     * @var string
     */
    protected $viewBasePath = 'invoice';

    /**
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return Invoice::query()
            ->where('client_id', Auth::user()->client->id);
    }

    protected function getIndexPageData(): array
    {
        $paymentGatewayResolver = app(PaymentGatewayResolver::class);
        $gateways = array_map(function($item){return $item['label'];}, $paymentGatewayResolver->all());
        return compact('gateways');
    }


    protected function getSearchableFields(): array
    {
        return [
            function (Builder $query, string $q) {
                if (($orderId = Invoice::decryptMask($q)) !== null) {
                    $query->where('invoices.id', $orderId);
                }
            }
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            [__('Creation Date'), 'invoices.created_at', iPaginationGenerator::SORT_DESC],
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  \App\Invoice $invoice
     * @param PaymentGatewayResolver $paymentGatewayResolver
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Request $request, Invoice $invoice, PaymentGatewayResolver $paymentGatewayResolver)
    {
        $this->authorize('view', $invoice);
        $gateways = array_map(function($item){return $item['label'];}, $paymentGatewayResolver->all());
        $invoice->load(['items.productType.category', 'country', 'province', 'payment']);
        $extended = config('nova.extended_invoice') ?? config('app.debug');
        $initiate = $request->has('initiate');
        return view('dashboard.client.invoice.show', compact('extended', 'gateways', 'initiate', 'invoice'));
    }

    /**
     * @param Request $request
     * @param  \App\Invoice $invoice
     * @param PaymentGatewayResolver $paymentGatewayResolver
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function pay(Request $request, Invoice $invoice, PaymentGatewayResolver $paymentGatewayResolver)
    {
        $this->authorize('view', $invoice);
        if(!$invoice->payable()){
            flash()->error(__('You cannot pay this invoice at the moment'));
            return redirect()->route('dashboard.client.invoice.show', compact('invoice'));
        }
        $request->validate(['gateway' => ['required', Rule::in(array_keys($paymentGatewayResolver->all()))]]);
        /** @var iPaymentGateway|null $paymentGateway */
        $paymentGateway = $paymentGatewayResolver->resolve($request->gateway);
        if(is_null($paymentGateway)){
            flash()->error(__('Please select another payment method'));
            return back();
        }

        return $paymentGateway->initiate($invoice);
    }
}
