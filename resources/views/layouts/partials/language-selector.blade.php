@php $languages = config('nova.language') @endphp
@if(count($languages) > 1)
    <li class="nav-item dropdown">
        <a id="languageDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
           aria-haspopup="true" aria-expanded="false" v-pre>
            {{--<i class="fa fa-globe"></i>--}}
            {{ $languages[app()->getLocale()]['display'] ?? ucfirst(app()->getLocale()) }}
            <span class="caret"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="languageDropdown">
            @foreach($languages as $code => $language)
                @continue($code === app()->getLocale())
                <a class="dropdown-item" href="Javascript:changeLanguage('{{ $code }}')">{{ $language['name'] }} <small>({{ $language['display'] }})</small></a>
            @endforeach
        </div>
    </li>
@endif

@push('before-body-ends')
    <script>
        function changeLanguage(code){
            var d = new Date();
            d.setMonth(d.getMonth() + 1);
            document.cookie = 'language=' + code + ';expires=' + d + ';path=/';
            location.reload();
        }
    </script>
@endpush