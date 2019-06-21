@php \App\Facades\UIManager::setActivePath('cms', 'post-categories') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Post Category list') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary" href="{{ route('dashboard.admin.cms.post-category.create') }}">{{ __('Create Post Category') }}</a></span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $postCategories,
                            'columnTitles' => [__('ID'),__('Code'),__('Language'),__('Name'),__('Parent Name'),__('address'),__('Active'),__('url'),__('Actions')],
                            'id' => 'post-category',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Category'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                            ],
                            'renderer' => 'dashboard.admin.cms.post-category.list-renderer',
                        ]){{ __('No category were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection