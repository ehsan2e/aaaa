@php
    $box = $box;
    if($errors->has('employee_number')){
        $currentStep = 1;
    }else if($errors->has('test')){
        $currentStep = 2;
    }else if($errors->has('admin_password') || $errors->has('domain')){
        $currentStep = 3;
    }else{
        $currentStep = 1;
    }
@endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="step-wizard ml-auto mr-auto">
                                <div class="step-wizard-row setup-panel">
                                    <div class="step-wizard-step">
                                        <a href="#" data-step="1" type="button"
                                           class="btn {{ ($errors->has('employee_number')) ? 'btn-danger' : 'btn-secondary' }} btn-circle"
                                        >1</a>
                                    </div>
                                    <div class="step-wizard-step">
                                        <a href="#" data-step="2" type="button"
                                           class="btn {{ ($errors->has('test')) ? 'btn-danger' : 'btn-secondary' }} btn-circle"
                                        >2</a>
                                    </div>
                                    <div class="step-wizard-step">
                                        <a href="#" data-step="3" type="button"
                                           class="btn {{ ($errors->has('admin_password') || $errors->has('domain')) ? 'btn-danger' : 'btn-secondary' }} btn-circle"
                                        >3</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form role="form" action="" method="post" id="hosted-pbx-form">
                            @csrf
                            <div class="step-wizard-content" data-step-content="1" style="display: none">
                                <h3 class="mb-4">{{ __('Tell us about your company') }}</h3>
                                <div class="employee-number-wrapper">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text"
                                                   for="employee_number">{{ __('Number of your employees?') }}</label>
                                        </div>
                                        <input type="text" name="employee_number" id="employee_number"
                                               class="form-control text-center @error('employee_number') is-invalid @enderror"
                                               value="{{ old('employee_number', $currentBox['employee_number'] ?? '1') }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                            <span class="text-center" id="suggested-box" style="min-width: 80px;">
                                               {{isset($box) ? ($box['price'] . $systemCurrencyCode): ''}}
                                            </span>
                                            </span>
                                        </div>
                                    </div>
                                    <span class="invalid-feedback" role="alert" style="display: none">
                                        <strong>{{ __('No box is available for company of this size please contact our support') }}</strong>
                                    </span>
                                    @error('employee_number')
                                    <span class="invalid-feedback custom-error" role="alert" style="display: block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="step-wizard-content" data-step-content="2" style="display: none">
                                <h3 class="mb-4">{{ __('Choose the services you are interested in') }}</h3>
                                <p class="mt-2">
                                    <b>{{ __('These services are already installed on all our boxes') }}</b>
                                </p>
                                @foreach($boxServices as $boxService)
                                    @continue(!($boxService->custom_attributes['pre_included'] || $boxService->custom_attributes['mandatory']))
                                    <div class="mb-2 ml-3">
                                        @if($boxService->type == \App\ProductType::TYPE_CONFIGURABLE)
                                            <div class="row">
                                                <div class="col-lg-3 col-md-4">{{ $boxService->name }}</div>
                                                <div class="col-lg-9 col-md-8">
                                                    <select name="box_services[]" class="form-control">
                                                        @unless($boxService->custom_attributes['mandatory'])
                                                            <option>{{ __('Select one if you want') }}</option>
                                                        @endunless
                                                        @foreach($boxService->simpleProducts as $simpleProduct)
                                                            <option value="{{ $simpleProduct->id }}"
                                                                @if($simpleProduct->id == ($boxService->complex_settings['default_product'] ?? -1)) selected @endif
                                                            >{{ $simpleProduct->name }} - @priceOrFreeOfCharge($simpleProduct->price)</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                        @else
                                        <label>
                                            @unless($boxService->custom_attributes['mandatory'])
                                                <input type="checkbox" name="box_services[]" value="{{ $boxService->id }}"
                                                       @if(in_array($boxService->id, old('box_services', $currentBox['box_services'] ?? [$boxService->id]))) checked @endif>
                                            @endunless
                                            {{ $boxService->name }} -
                                            @if($boxService->imposes_pre_invoice_negotiation ?? false)
                                                {{ __('Starting from') }} @priceOrFreeOfCharge($boxService->price)
                                                <div class="text-info ml-3">
                                                    <i class="fa fa-info-circle"></i>
                                                    {{ __('Final price will be determined through an interview after order gets placed') }}
                                                </div>
                                            @else
                                                @priceOrFreeOfCharge($boxService->price)
                                            @endif
                                        </label>
                                        @endif
                                    </div>
                                @endforeach
                                <p class="mt-2">
                                    <b>{{ __('You can select other services from the list below') }}</b>
                                </p>
                                @foreach($boxServices as $boxService)
                                    @continue($boxService->custom_attributes['pre_included'] || $boxService->custom_attributes['mandatory'])
                                    <div class="mb-2 ml-3">
                                        <label>
                                            <input type="checkbox" name="box_services[]" value="{{ $boxService->id }}"
                                                   @if(in_array($boxService->id, old('box_services', $currentBox['box_services'] ?? []))) checked @endif>
                                            {{ $boxService->name }} -
                                            @if($boxService->imposes_pre_invoice_negotiation ?? false)
                                                {{ __('Starting from') }} @priceOrFreeOfCharge($boxService->price)
                                                <div class="text-info ml-3"><i
                                                            class="fa fa-info-circle"></i> {{ __('Final price will be determined through an interview after order gets placed') }}
                                                </div>
                                            @else
                                                @priceOrFreeOfCharge($boxService->price)
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="step-wizard-content" data-step-content="3" style="display: none">
                                <h3 class="mb-4">{{ __('Some final questions:') }}</h3>
                                <div class="form-group row">
                                    <label for="admin_password" class="col-md-4 col-form-label text-md-right"
                                    >{{ __('Admin Password') }}</label>
                                    <div class="col-md-8">
                                        <input class="form-control @error('admin_password') is-invalid @enderror"
                                               type="password" id="admin_password" name="admin_password"
                                               value="{{ old('admin_password', $currentBox['admin_password'] ??  '') }}">
                                        @error('admin_password')
                                        <span class="invalid-feedback custom-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="admin_password_confirmation"
                                           class="col-md-4 col-form-label text-md-right"
                                    >{{ __('Admin Password Confirmation') }}</label>
                                    <div class="col-md-8">
                                        <input class="form-control" type="password" id="admin_password_confirmation"
                                               name="admin_password_confirmation"
                                               value="{{ old('admin_password_confirmation', $currentBox['admin_password'] ??  '') }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="domain" class="col-md-4 col-form-label text-md-right"
                                    >{{ __('Domain') }}</label>
                                    <div class="col-md-8">
                                        <input class="form-control @error('domain') is-invalid @enderror"
                                               type="text" id="domain" name="domain"
                                               value="{{ old('domain', $currentBox['domain'] ?? '') }}">
                                        @error('domain')
                                        <span class="invalid-feedback custom-error" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 clearfix">
                                <button class="btn btn-primary" type="button" id="rollback-wizard">
                                    <i class="fa fa-arrow-left"></i> {{ __('Previous') }}
                                </button>
                                <button class="btn btn-primary" type="button" id="commit-wizard">
                                    {{ __('Next') }} <i class="fa fa-arrow-right"></i>
                                </button>
                                <div class="pull-right">
                                    <button class="btn btn-success" type="submit" id="add-to-cart">
                                        <i class="fa fa-cart-plus"></i> {{ __('Add to Cart') }}
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

@push('before-body-ends')
    <script>
        (function ($) {
            var boxes = @json($boxes),
                currencyCode = '{{ $systemCurrencyCode }}',
                currentStep = {{ $currentStep }},
                lastStep = 3,
                submitableStep = {{ (is_null(old('_token')) && !isset($currentBox)) ? 'lastStep' : '1' }},
                forwardableStep = {{ (is_null(old('_token')) && !isset($currentBox)) ? 'currentStep' : 'lastStep' }},
                l = boxes.length,
                $wizardButtons = $('.step-wizard-step a'),
                $wizardContents = $('.step-wizard-content');

            function proceed(seekingStep) {
                currentStep = seekingStep || (currentStep + 1);
                if (currentStep > lastStep) {
                    currentStep = lastStep;
                    return;
                }
                submitableStep = Math.min(submitableStep, lastStep);
                forwardableStep = Math.max(forwardableStep, currentStep);

                if (currentStep === lastStep) {
                    submitableStep = 1;
                }

                $wizardButtons.each(function () {
                    var $this = $(this),
                        s = parseInt($this.data('step'));
                    if ($this.hasClass('btn-danger')) {
                        return;
                    }

                    if (s < currentStep) {
                        $this.removeClass('btn-success btn-primary btn-secondary btn-warning').addClass('btn-success');
                    } else if (s <= forwardableStep && s !== currentStep) {
                        $this.removeClass('btn-success btn-primary btn-secondary btn-warning').addClass('btn-primary');
                    } else if (s > currentStep) {
                        $this.removeClass('btn-success btn-primary btn-secondary btn-warning').addClass('btn-secondary');
                    } else {
                        $this.removeClass('btn-success btn-primary btn-secondary btn-warning').addClass('btn-warning');
                    }
                    $this = null;
                });

                $wizardContents.each(function () {
                    var $this = $(this),
                        s = parseInt($this.data('step-content'));

                    if (s === currentStep) {
                        $this.show();
                    } else {
                        $this.hide();
                    }
                    $this = null;
                });

                if (currentStep < lastStep) {
                    $('#commit-wizard').show()
                } else {
                    $('#commit-wizard').hide()
                }

                if (currentStep > 1) {
                    $('#rollback-wizard').show()
                } else {
                    $('#rollback-wizard').hide()
                }

                if (currentStep < submitableStep) {
                    $('#add-to-cart').hide()
                } else {
                    $('#add-to-cart').show()
                }
            }

            $wizardButtons.on('click', function (event) {
                event.preventDefault();
                var s = parseInt($(this).data('step'));
                console.log(s, forwardableStep, s > forwardableStep);
                if (s > forwardableStep) {
                    return;
                }
                proceed(s);
            });

            $('#commit-wizard').on('click', function (event) {
                proceed()
            });

            $('#rollback-wizard').on('click', function (event) {
                proceed(Math.max(1, currentStep - 1));
            });

            $('#hosted-pbx-form').on('submit', function (event) {
                if (currentStep < submitableStep) {
                    event.preventDefault();
                    proceed();
                    return;
                }
            });

            $('#employee_number').on('keypress', function (event) {
                var charCode = event.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                    event.preventDefault();
                    return;
                }
            }).on('keyup', function (event) {
                var $this = $(this),
                    value = parseInt($this.val());
                if (isNaN(value)) {
                    $('#suggested-box').html('');
                    return;
                }

                var $wrapper = $this.closest('.employee-number-wrapper'),
                    valid = false,
                    i;
                for (i = 0; i < l; i++) {
                    if ((boxes[i].max_employee === null) || (value <= boxes[i].max_employee)) {
                        $this.removeClass('is-invalid');
                        $wrapper.find('.invalid-feedback').hide();
                        $wrapper.find('.invalid-feedback.custom-error').hide();
                        $('#suggested-box').html(boxes[i].price + currencyCode);
                        valid = true;
                        break;
                    }
                }

                if (!valid) {
                    $('#suggested-box').html('');
                    $this.addClass('is-invalid');
                    $wrapper.find('.invalid-feedback').show();
                }

                $this = null;
                $wrapper = null;
            });
            proceed(currentStep);
        })(jQuery);
    </script>
@endpush