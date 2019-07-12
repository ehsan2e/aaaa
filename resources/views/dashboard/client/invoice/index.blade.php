@php \App\Facades\UIManager::setActivePath('invoices') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Invoice list') }}</span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $invoices,
                            'columnTitles' => [__('Invoice Number'), __('Sub Total'), __('Discount'), __('Tax'), __('Grand Total'), __('Status'),__('Date'),__('Actions')],
                            'id' => 'invoice',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Invoice'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                                'sortConfig' => $sortConfig ?? ['options' => [], 'orderBy' => -1, 'orderByParam' => 'order_by', 'sortDirection' => \NovaVoip\Interfaces\iPaginationGenerator::SORT_ASC, 'sortDirectionParam' => 'sort_order'],
                            ],
                            'renderer' => 'dashboard.client.invoice.list-renderer',
                        ]){{ __('No invoice were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('dashboard.client.invoice.payment-modal')
@endsection

