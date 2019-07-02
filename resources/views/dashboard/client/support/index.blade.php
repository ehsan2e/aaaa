@php \App\Facades\UIManager::setActivePath('support') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Ticket list') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary" href="{{ route('dashboard.client.support.create') }}">{{ __('Create New Ticket') }}</a></span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $tickets,
                            'columnTitles' => [__('Ticket Number'),__('Subject'),__('Category'),__('Status'),__('Created At'),__('Actions')],
                            'id' => 'support',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Ticket'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                            ],
                            'renderer' => 'dashboard.client.support.list-renderer',
                        ]){{ __('No ticket were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection