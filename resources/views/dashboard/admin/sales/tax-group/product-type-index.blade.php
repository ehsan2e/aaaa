@php \App\Facades\UIManager::setActivePath('sales', 'tax-group') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Product Type list') }}</span>
                            <span class="text-sm-center ml-auto">
                                <a class="btn btn-sm btn-primary"
                                   href="{{ route('dashboard.admin.sales.tax-group.index') }}">{{ __('Back') }}</a>
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
                            'collection' => $productTypes,
                            'columnTitles' => [__('ID'),__('SKU'),__('Name'),__('Price'),__('Category'),__('Supplier'),__('Supplier SKU'),__('Active'),__('Actions')],
                            'id' => 'product-type',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Product Type'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                            ],
                            'renderer' => 'dashboard.admin.sales.tax-group.product-type-list-renderer',
                        ]){{ __('No product type were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('before-body-ends')
    <form action="" method="post" id="remove-product-type-tax-group-form">
        @csrf
        @method('DELETE')
    </form>
    <script>
        jQuery('.remove-product-type-tax-group').on('click', function (event) {
            event.preventDefault();
            needsConfirmation("{{ __('Are you sure that you want to remove this tax group from the product?') }}", function(){
                var form = document.getElementById('remove-product-type-tax-group-form');
                form.setAttribute('action', this.getAttribute('href'));
                form.submit();
                form = null;
            }.bind(this));
        })
    </script>
@endpush