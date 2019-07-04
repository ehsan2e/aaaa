@php \App\Facades\UIManager::setActivePath('catalog', 'product-categories') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Product Category list') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary" href="{{ route('dashboard.admin.catalog.product-category.create') }}">{{ __('Create Product Category') }}</a></span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $productCategories,
                            'columnTitles' => [__('ID'),__('Code'),__('Name'),__('Parent Name'),__('address'),__('Active'),__('Actions')],
                            'id' => 'product-category',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Category'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                                'sortConfig' => $sortConfig ?? ['options' => [], 'orderBy' => -1, 'orderByParam' => 'order_by', 'sortDirection' => \NovaVoip\Interfaces\iPaginationGenerator::SORT_ASC, 'sortDirectionParam' => 'sort_order'],
                            ],
                            'renderer' => 'dashboard.admin.catalog.product-category.list-renderer',
                        ]){{ __('No category were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection