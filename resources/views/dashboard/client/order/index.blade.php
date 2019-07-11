@php \App\Facades\UIManager::setActivePath('orders') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Order list') }}</span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $orders,
                            'columnTitles' => [__('Order Number'), __('Sub Total'), __('Discount'), __('Tax'), __('Grand Total'), __('Status'),__('Date'),__('Actions')],
                            'id' => 'order',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Order'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                                'sortConfig' => $sortConfig ?? ['options' => [], 'orderBy' => -1, 'orderByParam' => 'order_by', 'sortDirection' => \NovaVoip\Interfaces\iPaginationGenerator::SORT_ASC, 'sortDirectionParam' => 'sort_order'],
                            ],
                            'renderer' => 'dashboard.client.order.list-renderer',
                        ]){{ __('No order were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection