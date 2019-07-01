@php \App\Facades\UIManager::setActivePath('crm', 'clients') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Client list') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary" href="{{ route('dashboard.admin.crm.client.create') }}">{{ __('Add Client') }}</a></span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $clients,
                            'columnTitles' => [__('ID'),__('Name'),__('Active'),__('Can Login'),__('Login Email'),__('Created At'),__('Actions')],
                            'id' => 'client',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Client'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                            ],
                            'renderer' => 'dashboard.admin.crm.client.list-renderer',
                        ]){{ __('No client were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection