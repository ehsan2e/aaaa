@php \App\Facades\UIManager::setActivePath('catalog', 'suppliers') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Edit Supplier') }}</span>
                            <span class="text-sm-center ml-auto">
                                <a class="btn btn-sm btn-primary"
                                   href="{{ route('dashboard.admin.supplier.index') }}">{{ __('Back') }}</a>
                            </span>
                        </nav>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('dashboard.admin.supplier.update', ['supplier' => $supplier]) }}"
                              method="post">
                            @method('PUT')
                            @include('dashboard.admin.supplier.form')

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Update Supplier') }}
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