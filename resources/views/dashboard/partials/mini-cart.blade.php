{{--<li class="nav-item dropdown">--}}
    {{--<a id="miniCartDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"--}}
       {{--aria-haspopup="true" aria-expanded="false" v-pre>--}}
        {{--<i class="fa fa-shopping-cart"></i>--}}
        {{--<span class="caret"></span>--}}
    {{--</a>--}}
    {{--<div class="dropdown-menu dropdown-menu-right" aria-labelledby="miniCartDropdown">--}}
        {{--<h6 class="dropdown-header">{{ __('Cart') }}</h6>--}}
    {{--</div>--}}
{{--</li>--}}
<li class="nav-item">
    <a class="nav-link" href="{{ route('cart') }}">
        <i class="fa fa-shopping-cart"></i>
        <span class="caret"></span>
    </a>
</li>