<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item @inactivepath('knowledgebase', 'active')">
                    <a class="nav-link" href="{{ route('knowledge-base.index') }}">
                        {{ __('Knowledge Base') }}
                    </a>
                </li>
                @auth
                    @includeWhen(Auth::user()->isAdmin(), 'dashboard.partials.admin-menu')
                    @includeWhen(Auth::user()->isClient(), 'dashboard.partials.client-menu')
                    @includeWhen(Auth::user()->isSupplier(), 'dashboard.partials.supplier-menu')
                @endauth
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
            @includeWhen(Auth::guest() || Auth::user()->isClient(), 'dashboard.partials.mini-cart')
            @include('layouts.partials.language-selector')
            <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login/Register') }}</a>
                    </li>
                @else
                    <li class="nav-item dropdown @inactivepath('account', 'active')">
                        <a id="accountDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="accountDropdown">
                            <a href="{{ route('dashboard.profile') }}" class="dropdown-item @inactivepath('profile', 'active')">{{ __('Profile') }}</a>
                            <a href="{{ route('dashboard.change-password') }}"
                               class="dropdown-item @inactivepath('change-password', 'active')">{{ __('Change Password') }}</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                  style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>