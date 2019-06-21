@php \App\Facades\UIManager::setActivePath('cms', 'posts') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Post list') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary" href="{{ route('dashboard.admin.cms.post.create') }}">{{ __('Create Post') }}</a></span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $posts,
                            'columnTitles' => [__('ID'),__('Code'),__('Language'),__('Author'),__('Category'),__('Title'),__('Active'),__('url'),__('Actions')],
                            'id' => 'post',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Post'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                            ],
                            'renderer' => 'dashboard.admin.cms.post.list-renderer',
                        ]){{ __('No post were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection