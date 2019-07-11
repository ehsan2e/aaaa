@php \App\Facades\UIManager::setActivePath('orders') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Order :order_number', ['order_number' => $order->order_number]) }}</span>
                            <span class="text-sm-center ml-auto">
                                <a class="btn btn-sm btn-primary"
                                   href="{{ route('dashboard.client.order.index') }}">{{ __('Back') }}</a>
                            </span>
                        </nav>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Date') }}:</b>
                                <span>@date($order->created_at, 'Y-m-d H:i')</span>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Status') }}</b>
                                @if($order->needs_negotiation)
                                    <span>{{ __('Waiting for interview') }}</span>
                                @elseif($order->can_be_invoiced && ($order->invoices_issued_count === 0))
                                    <span>{{ __('Pending invoice') }}</span>
                                @elseif($order->is_cancelled))
                                <span>{{ __('Cancelled') }}</span>
                                <span>@date($order->cancelled_at, 'Y-m-d H:i')</span>
                                @elseif($order->is_paid))
                                <span>{{ __('Paid') }}</span>
                                <span>@date($order->paid_at, 'Y-m-d H:i')</span>
                                @endif
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Sub Total') }}</b>
                                <span>{{ $order->sub_total }}</span>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Grand Total') }}</b>
                                <span>{{ $order->grand_total }}</span>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Discount') }}</b>
                                <span>{{ $order->discount }}</span>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Tax') }}</b>
                                <span>{{ __($order->tax) }}</span>
                            </div>
                            <div class="col-lg-6 col-sm-8">
                                <b>{{ __('Tax Region') }}</b>
                                <span>{{ \NovaVoip\translateEntity($order->country) }}
                                    - {{ \NovaVoip\translateEntity($order->province) }}</span>
                            </div>
                        </div>
                        <table class="table mt-3">
                            <thead>
                            <tr>
                                <th></th>
                                <th>{{ __('Item') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Price') }}
                                    <small>({{ $systemCurrencyCode }})</small>
                                </th>
                                <th>{{ __('Sub Total') }}
                                    <small>({{ $systemCurrencyCode }})</small>
                                </th>
                                @if($extended)
                                    <th>{{ __('Discount') }}</th>
                                    <th>{{ __('Tax') }}</th>
                                    <th>{{ __('Grand Total') }}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @component('dashboard.client.order.row', ['items' => $order->items, 'extended' => $extended, 'order' => $order]) @endcomponent
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection