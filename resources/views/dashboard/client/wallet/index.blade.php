@php
    \App\Facades\UIManager::setActivePath('wallet');
    $filteredFromDate = \Illuminate\Support\Facades\Request::query('from_date', '');
    $filteredToDate = \Illuminate\Support\Facades\Request::query('to_date', '');
@endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Wallet') }}</span>
                            <span class="text-sm-center ml-auto">
                                <a class="btn btn-sm btn-primary"
                                   href="{{ route('dashboard.client.wallet.charge') }}"
                                >{{ __('Charge Wallet') }}</a>
                            </span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $transactions,
                            'columnTitles' => [__('Old Balance'), __('Amount'), __('New Balance'), __('Type'), __('Date'),__('Description')],
                            'id' => 'transaction',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Transaction'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                                'sortConfig' => $sortConfig ?? ['options' => [], 'orderBy' => -1, 'orderByParam' => 'order_by', 'sortDirection' => \NovaVoip\Interfaces\iPaginationGenerator::SORT_ASC, 'sortDirectionParam' => 'sort_order'],
                            ],
                            'renderer' => 'dashboard.client.wallet.list-renderer',
                        ]){{ __('No transaction were found') }}
                        @slot('extraControls')
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    @component('dashboard.components.input-box', ['name' => 'from_date', 'defaultValue' => $filteredFromDate, 'inline' => true]){{ __('From') }}@endcomponent
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    @component('dashboard.components.input-box', ['name' => 'to_date', 'defaultValue' => $filteredToDate, 'inline' => true]){{ __('To') }}@endcomponent
                                </div>
                            </div>
                        @endslot
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection