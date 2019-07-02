<form action="" method="get" id="{{ 'listing-search-form-' . $id }}">
    <div class="input-group mb-3">
        @if($canRunRawQuery)
            <div class="input-group-prepend">
                <button class="btn btn-outline-secondary" id="basic-addon1" type="button"
                        onclick="(function(){var el = document.getElementById('{{ 'listing-search-form-query-' . $id }}'); el.value = (/^:::>/.test(el.value) ? '' : ':::>') + (el.value || ''); el=null;})()">
                    <span class="fa fa-magic"></span></button>
            </div>
        @endif
        <input type="text" name="{{ $queryParamName ?? 'q' }}"
               class="form-control {{ isset($queryError) ? 'is-invalid' : ''}}"
               id="{{ 'listing-search-form-query-' . $id }}"
               placeholder="{{ $placeholder ?? ''}}"
               aria-label="{{ __('Search image') }}" aria-describedby="basic-addon2"
               value="{{ \Illuminate\Support\Facades\Request::query($queryParamName ?? 'q', '') }}"
        >
        {!! $inlineFilters ?? '' !!}
        <div class="input-group-append">
            <button class="btn btn-primary" type="submit">{{ $searchCaption ?? __('Search') }}</button>
            @if($hasClear ?? true)
                <a class="btn btn-outline-primary"
                   href="Javascript:(function(){location.href=location.pathname;})()">{{ $clearCaption ?? __('Clear') }}</a>
            @endif
            {!! $slot !!}
        </div>
        @if(isset($queryError))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $queryError }}</strong>
            </span>
        @endif
    </div>
    @if(isset($extraControls) && (strlen(trim($extraControls)) > 0))
        <div class="collapse" id="{{ 'listing-search-form-extra-control' . $id }}">
            <div class="mb-2">
                {!! $extraControls !!}
            </div>
        </div>
        <p class="text-center">
            <a class="" data-toggle="collapse" href="#{{ 'listing-search-form-extra-control' . $id }}" role="button"
               aria-expanded="false"
               aria-controls="{{ 'listing-search-form-extra-control' . $id }}">{{ $extraControlsTitle ?? __('Advanced Search Options') }}</a>
        </p>
    @endif
</form>