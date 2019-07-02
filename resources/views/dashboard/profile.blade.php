@php \App\Facades\UIManager::setActivePath('account', 'profile') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Profile') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary" href="{{ route('dashboard.change-password') }}">{{ __('Change Password') }}</a></span>
                        </nav>
                    </div>

                    <div class="card-body">
                        <form action="" method="post">
                            @csrf
                            @component('dashboard.components.input-box', ['name' => 'name', 'model' => \Illuminate\Support\Facades\Auth::user(), 'required' => true, 'autofocus' => true]){{ __('Name') }}@endcomponent

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit') }}
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