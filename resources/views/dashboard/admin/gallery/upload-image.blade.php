@php \App\Facades\UIManager::setActivePath('cms', 'gallery') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Create Supplier') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary" href="{{ route('dashboard.admin.supplier.index') }}">{{ __('Back') }}</a></span>
                        </nav>
                    </div>

                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            @csrf
                            @component('dashboard.components.input-box', ['name' => 'name', 'required' => true, 'autofocus' => true]){{ __('Name') }}@endcomponent
                            @component('dashboard.components.upload', ['name' => 'image', 'required' => true]){{ __('Name') }}@endcomponent

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Upload') }}
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