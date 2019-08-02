@php \App\Facades\UIManager::setActivePath('catalog', 'product-types') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <nav class="nav nav-pills flex-column flex-sm-row">
                    <span class="text-sm-center">{{ __('Create Simple Product') }}</span>
                    <span class="text-sm-center ml-auto">
                        <a class="btn btn-sm btn-primary"
                           href="{{ route('dashboard.admin.catalog.product-type.create') }}">{{ __('Back') }}</a>
                    </span>
                </nav>
            </div>
            <div class="card-body">
                <form action="{{ route('dashboard.admin.catalog.product-type.store') }}" method="post">
                    @include('dashboard.admin.catalog.product-type.form')
                    <hr>
                    <div class="clearfix">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Create Product') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


