@php \App\Facades\UIManager::setActivePath('account', 'change-password') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Change Password') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary" href="{{ route('dashboard.profile') }}">{{ __('Profile') }}</a></span>
                        </nav>
                    </div>

                    <div class="card-body">
                        <form action="" method="post">
                            @csrf
                            @component('dashboard.components.password', ['name' => 'current_password', 'required' => true]){{ __('Current Password') }}@endcomponent
                            @component('dashboard.components.password', ['name' => 'password', 'required' => true, 'withConfirmation' => true])
                                {{ __('New Password') }}
                                @slot('cofirmationLabel') {{ __('Confirm New Password') }} @endslot
                            @endcomponent

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