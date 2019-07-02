@php \App\Facades\UIManager::setActivePath('crm', 'ticket') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Ticket list') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary" href="{{ route('dashboard.admin.crm.ticket.create') }}">{{ __('Create Ticket') }}</a></span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $tickets,
                            'columnTitles' => [__('ID'),__('Subject'),__('Category'),__('Users'),__('Status'),__('Actions')],
                            'id' => 'ticket',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Ticket'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                            ],
                            'renderer' => 'dashboard.admin.crm.ticket.list-renderer',
                        ]){{ __('No ticket were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection