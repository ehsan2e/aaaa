@php \App\Facades\UIManager::setActivePath('catalog', 'product-types') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <nav class="nav nav-pills flex-column flex-sm-row">
                    <span class="text-sm-center">{{ __('Create :type Product', ['type' => $types[$type]]) }}</span>
                    <span class="text-sm-center ml-auto">
                        <a class="btn btn-sm btn-primary"
                           href="{{ route('dashboard.admin.catalog.product-type.create') }}">{{ __('Back') }}</a>
                    </span>
                </nav>
            </div>
            <div class="card-body">
                <form action="{{ route('dashboard.admin.catalog.product-type.store') }}" method="post" id="product-type-form">
                    @include('dashboard.admin.catalog.product-type.form-' . $slugs[$type])
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


