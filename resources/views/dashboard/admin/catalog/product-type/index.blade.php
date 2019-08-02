@php
    \App\Facades\UIManager::setActivePath('catalog', \Illuminate\Support\Facades\Request::query('category_code', 'any-product') . '-category');
    $filteredActive = \Illuminate\Support\Facades\Request::query('active', '');
    $filteredCategory = \Illuminate\Support\Facades\Request::query('category', '');
    if($filteredCategory === ''){
        $filteredCategoryCode = \Illuminate\Support\Facades\Request::query('category_code', '');
        if($filteredCategoryCode !== ''){
            $filteredCategory = array_filter($productCategories->toArray(), function($productCategory) use ($filteredCategoryCode){ return $productCategory['code'] === $filteredCategoryCode;})[0]['id'] ?? '';
        }
    }
@endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Product Type list') }}</span>
                            <span class="text-sm-center ml-auto">
                                <a class="btn btn-sm btn-primary"
                                   href="{{ route('dashboard.admin.catalog.product-type.create') }}"
                                >{{ __('Create Product Type') }}</a>
                            </span>
                        </nav>
                    </div>
                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $productTypes,
                            'columnTitles' => [__('ID'),__('SKU'),__('Name'),__('Category'),__('Price'),__('Category'),__('Supplier'),__('Supplier SKU'),__('Active'),__('Actions')],
                            'id' => $listId ?? 'product-type',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Product Type'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                                'sortConfig' => $sortConfig ?? ['options' => [], 'orderBy' => -1, 'orderByParam' => 'order_by', 'sortDirection' => \NovaVoip\Interfaces\iPaginationGenerator::SORT_ASC, 'sortDirectionParam' => 'sort_order'],
                            ],
                            'renderer' => $renderer ?? 'dashboard.admin.catalog.product-type.list-renderer',
                        ]){{ __('No product type were found') }}
                        @slot('searchBarInlineFilters')
                            <select name="category" class="form-control less-significant">
                                <option value="">{{ __('All categories') }}</option>
                                @foreach($productCategories as $productCategory)
                                    @component('dashboard.components.option', ['value'=>$productCategory->id, 'selectedValue' => $filteredCategory]){{ $productCategory->name }}@endcomponent
                                @endforeach
                            </select>
                        @endslot
                        @slot('extraControls')
                            <div class="row">
                                <div class="col-lg-6">
                                    <span>{{ __('Active') }}</span>
                                    @component('dashboard.components.option', ['name' => 'active', 'value'=>'', 'selectedValue' => $filteredActive, 'inline' => true, 'type' => 'single-choice']){{ __('all') }}@endcomponent
                                    @component('dashboard.components.option', ['name' => 'active', 'value'=>'1', 'selectedValue' => $filteredActive, 'inline' => true, 'type' => 'single-choice']){{ __('active') }}@endcomponent
                                    @component('dashboard.components.option', ['name' => 'active', 'value'=>'0', 'selectedValue' => $filteredActive, 'inline' => true, 'type' => 'single-choice']){{ __('inactive') }}@endcomponent
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