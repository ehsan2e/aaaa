@php \App\Facades\UIManager::setActivePath('sales', 'tax-group') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Tax Group list') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary" href="{{ route('dashboard.admin.sales.tax-group.create') }}">{{ __('Create Tax Group') }}</a></span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $taxGroups,
                            'columnTitles' => [__('ID'),__('Name'),__('Amount'),__('Active'),__('Percentage'),__('Actions')],
                            'id' => 'tax-group',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Tax Group'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                                'sortConfig' => $sortConfig ?? ['options' => [], 'orderBy' => -1, 'orderByParam' => 'order_by', 'sortDirection' => \NovaVoip\Interfaces\iPaginationGenerator::SORT_ASC, 'sortDirectionParam' => 'sort_order'],
                            ],
                            'renderer' => 'dashboard.admin.sales.tax-group.list-renderer',
                        ]){{ __('No tax group were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection