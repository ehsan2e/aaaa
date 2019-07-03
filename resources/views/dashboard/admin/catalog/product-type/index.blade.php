@php \App\Facades\UIManager::setActivePath('catalog', \Illuminate\Support\Facades\Request::query('category_code', 'any-product') . '-category') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Product Type list') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary" href="{{ route('dashboard.admin.catalog.product-type.create') }}">{{ __('Create Product Type') }}</a></span>
                        </nav>
                    </div>
                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $productTypes,
                            'columnTitles' => [__('ID'),__('SKU'),__('Name'),__('Price'),__('Category'),__('Supplier'),__('Supplier SKU'),__('Active'),__('Actions')],
                            'id' => 'product-type',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Product Type'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                            ],
                            'renderer' => 'dashboard.admin.catalog.product-type.list-renderer',
                        ]){{ __('No product type were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection