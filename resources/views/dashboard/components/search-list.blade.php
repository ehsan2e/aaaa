@php
    $layout = $layout ?? 'SB^E^T^S <div class="pull-right"> P </div>';
    $sortConfig = $sortConfig ?? ['options' => [], 'orderBy' => -1, 'orderByParam' => 'order_by', 'sortDirection' => \NovaVoip\Interfaces\iPaginationGenerator::SORT_ASC, 'sortDirectionParam' => 'sort_order'];
    $sortDirectionLabels = [
        \NovaVoip\Interfaces\iPaginationGenerator::SORT_ASC => __('Ascending'),
        \NovaVoip\Interfaces\iPaginationGenerator::SORT_DESC => __('Descending'),
    ];
@endphp

<form action="" method="get" id="{{ 'listing-search-form-' . $id }}">
    @foreach(explode('^', $layout) as $layoutRow)
        <div class="mb-3">
            @foreach(explode(' ', $layoutRow) as $layoutPart)
                @switch($layoutPart)
                    @case('E')
                    @if(isset($extraControls) && (strlen(trim($extraControls)) > 0))
                        <div class="collapse" id="{{ 'listing-search-form-extra-control' . $id }}">
                            <div class="mb-2">
                                {!! $extraControls !!}
                            </div>
                        </div>
                        <p class="text-center">
                            <a class="" data-toggle="collapse" href="#{{ 'listing-search-form-extra-control' . $id }}"
                               role="button"
                               aria-expanded="false"
                               aria-controls="{{ 'listing-search-form-extra-control' . $id }}">{{ $extraControlsTitle ?? __('Advanced Search Options') }}</a>
                        </p>
                    @endif
                    @break
                    @case('P')
                    <div class="d-inline-block">
                        {!! $pagination ?? '' !!}
                    </div>
                    @break
                    @case('S')
                    @if($sortConfig['orderBy'] !== -1)
                        <div class="listing-sort d-inline-block">
                            <input type="hidden" data-role="order_by" name="{{ $sortConfig['orderByParam'] }}"
                                   value="{{ $sortConfig['orderBy'] }}">
                            <input type="hidden" data-role="sort_order" name="{{ $sortConfig['sortDirectionParam'] }}"
                                   value="{{ $sortConfig['sortDirection'] }}">
                            <span class="text-secondary mr-3">{{ __('Sort list by') }}</span>
                            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                            id="{{ 'list-search-sorting-' . $id }}" data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                        {{ $sortConfig['options'][$sortConfig['orderBy']] }}
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="{{ 'list-search-sorting-' . $id }}">
                                        @foreach($sortConfig['options'] as $index => $label)
                                            <a class="dropdown-item" href="#" data-target="order_by"
                                               data-value="{{ $index }}">{{ $label }}</a>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                            id="{{ 'list-search-sorting-order' . $id }}" data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                        {{ $sortDirectionLabels[$sortConfig['sortDirection']] }}
                                    </button>
                                    <div class="dropdown-menu"
                                         aria-labelledby="{{ 'list-search-sorting-order' . $id }}">
                                        @foreach($sortDirectionLabels as $value => $label)
                                            <a class="dropdown-item" href="#" data-target="sort_order"
                                               data-value="{{ $value }}">{{ $label }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @break
                    @case('SB')
                    <div class="input-group">
                        <div class="input-group-prepend">
                            @if($canRunRawQuery)
                                <button class="btn btn-outline-secondary" id="basic-addon1" type="button"
                                        onclick="(function(){var el = document.getElementById('{{ 'listing-search-form-query-' . $id }}'); el.value = (/^:::>/.test(el.value) ? '' : ':::>') + (el.value || ''); el=null;})()">
                                    <span class="fa fa-magic"></span></button>
                            @endif

                        </div>
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
                    @break
                    @case('T')
                    {!! $table ?? '' !!}
                    @break
                    @default
                    {!! $layoutPart !!}
                @endswitch
            @endforeach
        </div>
    @endforeach
</form>