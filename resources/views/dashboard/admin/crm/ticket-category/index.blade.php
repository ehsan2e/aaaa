@php
    \App\Facades\UIManager::setActivePath('crm', 'ticket-category');
    $filteredType = \Illuminate\Support\Facades\Request::query('type', '');
    $filteredActive = \Illuminate\Support\Facades\Request::query('active', '');
@endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Ticket Category list') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary"
                                                                    href="{{ route('dashboard.admin.crm.ticket-category.create') }}">{{ __('Create Ticket Category') }}</a></span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $ticketCategories,
                            'columnTitles' => [__('ID'),__('Title'),__('Type'),__('Active'),__('Actions')],
                            'id' => 'ticket-category',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Ticket Category'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                            ],
                            'renderer' => 'dashboard.admin.crm.ticket-category.list-renderer',
                        ]){{ __('No ticket category were found') }}
                        @slot('searchBarInlineFilters')
                            <select name="type" class="form-control">
                                <option value="">{{ __('All types') }}</option>
                                @foreach($types as $value => $label)
                                    @component('dashboard.components.option', ['value'=>$value, 'selectedValue' => $filteredType]){{ $label }}@endcomponent
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