@php \App\Facades\UIManager::setActivePath('sales', 'tax-group') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Tax Rule list') }}</span>
                            <span class="text-sm-center ml-auto">
                                <a class="btn btn-sm btn-primary"
                                   href="{{ route('dashboard.admin.sales.tax-group.index') }}"
                                >{{ __('Return to Tax Group List') }}</a>
                                <a class="btn btn-sm btn-primary"
                                   href="{{ route('dashboard.admin.sales.tax-rule.create', ['tax_group' => $taxGroup]) }}"
                                >{{ __('Create Tax Rule') }}</a>
                            </span>
                        </nav>
                    </div>

                    <div class="card-body">
                        <h4>Tax Group: {{ \NovaVoip\translateEntity($taxGroup) }}
                            <i class="fa fa-circle {{ $taxGroup->active ? 'text-success' : 'text-danger' }}"
                               title="{{ $taxGroup->active ? __('Active') : __('Inactive') }}"
                               data-toggle="tooltip"></i>
                        </h4>
                        <div>
                            <span>{{ __('Amount:') . " {$taxGroup->amount}" }} </span>
                            <div class="badge badge-secondary">{{ $taxGroup->is_percentage ? __('Percentage') : __('Fixed') }}</div>
                        </div>
                        <hr class="my-3">
                        @component('dashboard.components.pagination', [
                            'collection' => $taxRules,
                            'columnTitles' => [__('ID'),__('Country'),__('Province'),__('Priority'),__('Amount'),__('Active'),__('Percentage'),__('Actions')],
                            'id' => 'tax-rule',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Tax Rule'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                                'sortConfig' => $sortConfig ?? ['options' => [], 'orderBy' => -1, 'orderByParam' => 'order_by', 'sortDirection' => \NovaVoip\Interfaces\iPaginationGenerator::SORT_ASC, 'sortDirectionParam' => 'sort_order'],
                            ],
                            'renderer' => 'dashboard.admin.sales.tax-rule.list-renderer',
                            'rendererData' => compact('taxGroup')
                        ]){{ __('No tax rule were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('before-body-ends')
    <form action="" method="post" id="remove-rule-form">
        @csrf
        @method('DELETE')
    </form>
    <script>
        jQuery('.remove-rule').on('click', function (event) {
            event.preventDefault();
            needsConfirmation("{{ __('Are you sure that you want to remove this rule?') }}", function(){
                var form = document.getElementById('remove-rule-form');
                    form.setAttribute('action', this.getAttribute('href'));
                    form.submit();
                    form = null;
            }.bind(this));
        })
    </script>
@endpush