@php \App\Facades\UIManager::setActivePath('sales', 'payment') @endphp
@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <nav class="nav nav-pills flex-column flex-sm-row">
                            <span class="text-sm-center">{{ __('Payment :payment_number(#:id)', ['id' => $payment->id, 'payment_number' => $payment->payment_number]) }}</span>
                            <span class="text-sm-center ml-auto">
                                <a class="btn btn-sm btn-primary"
                                   href="{{ route('dashboard.admin.sales.payment.index') }}">{{ __('Back') }}</a>
                            </span>
                        </nav>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Date') }}:</b>
                                <span>@date($payment->created_at, 'Y-m-d H:i')</span>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Amount') }}:</b>
                                <span>{{ $payment->amount }}</span>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Gateway') }}:</b>
                                <span>{{ $payment->gateway }}</span>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <b>{{ __('Status') }}:</b>
                                <span>{{ $payment->status_caption }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">{{ __('Process Data') }}</div>
                    <table class="table table-striped mb-0">
                        <thead>
                        <tr>
                            <th>{{ __('Key') }}</th>
                            <th>{{ __('Value') }}</th>
                        </tr>
                        </thead>
                        @forelse($payment->process_data ?? [] as $key => $value)
                            <tr>
                                <td>{{ $key }}</td>
                                <td style="word-break: break-all">
                                    @if(is_array($value))
                                        <ul>
                                            @foreach($value as $k=> $v)
                                                <li>
                                                    <b>{{ $k }}</b>
                                                    <span>{{ is_array($v)? json_encode($v): (is_bool($v) ? ($v ? 'True': 'False') : (is_null($v) ? 'Null' : $v)) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @elseif(is_bool($value))
                                        {{ $value ? 'True': 'False' }}
                                    @elseif(is_null($value))
                                        Null
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">{{ __('No item exists') }}</td>
                            </tr>
                        @endforelse
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">{{ __('Information') }}</div>
                    <table class="table table-striped mb-0">
                        <thead>
                        <tr>
                            <th>{{ __('Key') }}</th>
                            <th>{{ __('Value') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($payment->information ?? [] as $key => $value)
                        <tr>
                            <td>{{ $key }}</td>
                            <td style="word-break: break-all">
                                @if(is_array($value))
                                    <ul>
                                        @foreach($value as $k=> $v)
                                            <li>
                                                <b>{{ $k }}</b>
                                                <span>{{ is_array($v)? json_encode($v): (is_bool($v) ? ($v ? 'True': 'False') : (is_null($v) ? 'Null' : $v)) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @elseif(is_bool($value))
                                    {{ $value ? 'True': 'False' }}
                                @elseif(is_null($value))
                                    Null
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">{{ __('No item exists') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection