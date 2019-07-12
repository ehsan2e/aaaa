@php \App\Facades\UIManager::setActivePath('invoices') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Invoice :invoice_number', ['invoice_number' => $invoice->invoice_number]) }}</span>
                            <span class="text-sm-center ml-auto">
                                @if($invoice->payable())
                                    <a class="btn btn-sm btn-success" data-toggle="modal" data-target="#paymentModal"
                                       href = "{{ route('dashboard.client.invoice.pay', compact('invoice')) }}"
                                       data-can-use-credit="{{ $item->can_be_paid_by_credit ? 'yes' : 'no' }}"
                                    >{{ __('Pay Invoice') }}</a>
                                @endif
                                <a class="btn btn-sm btn-primary"
                                   href="{{ route('dashboard.client.invoice.index') }}">{{ __('Back') }}</a>
                            </span>
                        </nav>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Date') }}:</b>
                                <span>@date($invoice->created_at, 'Y-m-d H:i')</span>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Status') }}</b>
                                @component('dashboard.client.invoice.status', compact('invoice')) @endcomponent
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Sub Total') }}</b>
                                <span>{{ $invoice->sub_total }}</span>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Grand Total') }}</b>
                                <span>{{ $invoice->grand_total }}</span>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Discount') }}</b>
                                <span>{{ $invoice->discount }}</span>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Tax') }}</b>
                                <span>{{ __($invoice->tax) }}</span>
                            </div>
                            <div class="col-lg-6 col-sm-8">
                                <b>{{ __('Tax Region') }}</b>
                                <span>{{ \NovaVoip\translateEntity($invoice->country) }}
                                    - {{ \NovaVoip\translateEntity($invoice->province) }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Payment Number') }}:</b>
                                <span>{{ $invoice->payment->payment_number ?? '-' }}</span>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Payment Date') }}:</b>
                                <span>@if($invoice->paid_at) @date($invoice->paid_at, 'Y-m-d H:i') @else - @endif</span>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Reference Number') }}:</b>
                                <span>{{ $invoice->payment->reference_number ?? ($invoice->paid_at ? __('By credit') : '-') }}</span>
                            </div>
                        </div>
                        <table class="table mt-3">
                            <thead>
                            <tr>
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
                            @component('dashboard.client.invoice.row', ['items' => $invoice->items, 'extended' => $extended, 'invoice' => $invoice]) @endcomponent
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('dashboard.client.invoice.payment-modal', ['action' => route('dashboard.client.invoice.pay', compact('invoice')), 'canUseCredit' => $invoice->can_be_paid_by_credit])
@endsection