@php \App\Facades\UIManager::setActivePath('crm', 'clients') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Client list') }}</span>
                            <span class="text-sm-center ml-auto"><a class="btn btn-sm btn-primary"
                                                                    href="{{ route('dashboard.admin.crm.client.create') }}">{{ __('Add Client') }}</a></span>
                        </nav>
                    </div>

                    <div class="card-body">
                        @component('dashboard.components.pagination', [
                            'collection' => $clients,
                            'columnTitles' => [__('ID'),__('Name'),__('Active'),__('Can Login'),__('Login Email'),__('Created At'),__('Actions')],
                            'id' => 'client',
                            'searchBoxConfig' => [
                                'canRunRawQuery' => $canRunRawQuery ?? false,
                                'placeholder' => __('Search Client'),
                                'queryError' => $queryError ?? null,
                                'queryParamName' => $queryParamName ?? 'q',
                                'sortConfig' => $sortConfig ?? ['options' => [], 'orderBy' => -1, 'orderByParam' => 'order_by', 'sortDirection' => \NovaVoip\Interfaces\iPaginationGenerator::SORT_ASC, 'sortDirectionParam' => 'sort_order'],
                            ],
                            'renderer' => 'dashboard.admin.crm.client.list-renderer',
                        ]){{ __('No client were found') }}@endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('before-body-ends')
    @can(\App\Ability::LOGIN_AS_CLIENT)
        <!-- Login As Modal -->
        <div class="modal fade" id="loginAsModal" tabindex="-1" role="dialog" aria-labelledby="loginAsModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="loginAsModalLabel">Login as <span id="client-name"></span></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @component('dashboard.components.password', ['name' => 'password', 'required' => true]){{ __('Your Password') }}@endcomponent
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Login') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            (function($){
                var $loginModal = $('#loginAsModal');
                $loginModal.on('show.bs.modal', function (event) {
                    var $relatedTarget = $(event.relatedTarget);
                    $loginModal.find('form').attr('action', $relatedTarget.attr('href'));
                    $loginModal.find('#client-name').html($relatedTarget.data('client-name'));
                    $relatedTarget = null;
                });
                $loginModal.on('shown.bs.modal', function (event) {
                    $loginModal.find('input[type=password').focus()
                });

                })(jQuery)
        </script>
    @endcan
@endpush