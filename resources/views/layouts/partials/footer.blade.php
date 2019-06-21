@php $currentRouteName=\Illuminate\Support\Facades\Route::currentRouteName() @endphp
<div class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="collapse navbar-collapse" id="footerNavBar">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item {{ $currentRouteName === 'about-us' ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('about-us') }}">{{ __('About Us') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentRouteName === 'terms-and-services' ? 'active' : '' }}" href="{{ route('terms-and-services') }}">{{ __('Terms and Services') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentRouteName === 'privacy-policy' ? 'active' : '' }}" href="{{ route('privacy-policy') }}">{{ __('Privacy Policy') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentRouteName === 'contact-us' ? 'active' : '' }}" href="{{ route('contact-us') }}">{{ __('Contact Us') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $currentRouteName === 'faq' ? 'active' : '' }}" href="{{ route('faq') }}">{{ __('FAQ') }}</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span style="color: #858585"><i class="fa fa-copyright"></i> {{ __('All rights reserved') }}</span>
                </li>
            </ul>
        </div>
    </nav>
</div>