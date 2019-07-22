@php \App\Facades\UIManager::setActivePath('system', 'box-monitor') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Monitor Boxes') }}</span>
                        </nav>
                    </div>

                    <div class="card-body">
                        <box-monitor-component url="{{ $wsUrl }}"></box-monitor-component>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

