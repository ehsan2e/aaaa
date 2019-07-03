@php \App\Facades\UIManager::setActivePath('sales', 'tax-group') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Create Tax Rule') }}</span>
                            <span class="text-sm-center ml-auto">
                                <a class="btn btn-sm btn-primary"
                                   href="{{ route('dashboard.admin.sales.tax-rule.index', ['tax_group' => $taxGroup]) }}">{{ __('Back') }}</a>
                            </span>
                        </nav>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('dashboard.admin.sales.tax-rule.store', ['tax_group' => $taxGroup]) }}" method="post">
                            @include('dashboard.admin.sales.tax-rule.form')

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Create Tax Rule') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection