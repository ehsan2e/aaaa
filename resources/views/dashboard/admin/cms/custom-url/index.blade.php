@php \App\Facades\UIManager::setActivePath('cms', 'custom-urls') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Url list') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary" href="{{ route('dashboard.admin.cms.custom-url.create') }}">{{ __('Define Url') }}</a></span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $customUrls,
                            'columnTitles' => [__('ID'),__('Path'),__('Target'),__('Redirect Type'),__('Handler'),__('Actions')],
                            'id' => 'custom-url',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Url'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                            ],
                            'renderer' => 'dashboard.admin.cms.custom-url.list-renderer',
                        ]){{ __('No url were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection