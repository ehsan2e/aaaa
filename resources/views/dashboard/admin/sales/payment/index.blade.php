@php
    \App\Facades\UIManager::setActivePath('sales', 'payment');
    $filteredStatus = \Illuminate\Support\Facades\Request::query('status', '');
@endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Payment list') }}</span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $payments,
                            'columnTitles' => [__('ID'), __('Payment Number'), __('Gateway'), __('Amount'), __('Reference Number'), __('Status'),__('Date'), __('Action')],
                            'id' => 'payment',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Payment'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                                'sortConfig' => $sortConfig ?? ['options' => [], 'orderBy' => -1, 'orderByParam' => 'order_by', 'sortDirection' => \NovaVoip\Interfaces\iPaginationGenerator::SORT_ASC, 'sortDirectionParam' => 'sort_order'],
                            ],
                            'renderer' => 'dashboard.admin.sales.payment.list-renderer',
                        ])
                            {{ __('No payment were found') }}
                            @slot('searchBarInlineFilters')
                                <select name="status" class="form-control less-significant">
                                    <option value="">{{ __('All statuses') }}</option>
                                    @foreach(\App\Payment::getStatuses() as $status => $caption)
                                        @component('dashboard.components.option', ['value'=>$status, 'selectedValue' => $filteredStatus]){{ $caption }}@endcomponent
                                    @endforeach
                                </select>
                            @endslot
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

