@php \App\Facades\UIManager::setActivePath('catalog', 'suppliers') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Supplier list') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary" href="{{ route('dashboard.admin.catalog.supplier.create') }}">{{ __('Add Supplier') }}</a></span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $suppliers,
                            'columnTitles' => [__('ID'),__('Name'),__('Active'),__('Can Login'),__('Login Email'),__('Created At'),__('Actions')],
                            'id' => 'supplier',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Supplier'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                            ],
                            'renderer' => 'dashboard.admin.catalog.supplier.list-renderer',
                        ]){{ __('No supplier were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection