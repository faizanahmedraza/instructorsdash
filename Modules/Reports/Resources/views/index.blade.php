@extends('core::layouts.app')
@section('title', __('Reports'))
@push('head')
    <style>
        .cstm-card {
            max-height: 300px;
            overflow-y: auto;
        }

        .ext-margin {
            margin: 10px 0px;
        }
    </style>
@endpush
@section('content')
    {{-- Start Admin Dashboard --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">@lang('Reports')</h1>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="card ext-margin">
                <div class="card-header">
                    <div class="card-title">Course Registers</div>
                </div>
                <div class="card-body cstm-card">
                    @forelse($coursesRegisterAndSales as $key => $val)
                        <div class="d-flex justify-content-between mb-2">
                            <p>{{$val->name}}</p>
                            <p class="badge badge-success">{{$val->registered}}</p>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card ext-margin">
                <div class="card-header">
                    <div class="card-title">Course Sales</div>
                </div>
                <div class="card-body cstm-card">
                    @forelse($coursesRegisterAndSales as $key => $val)
                        <div class="d-flex justify-content-between mb-2">
                            <p>{{$val->name}}</p>
                            <p class="badge badge-success">${{!empty($val->sales) ? $val->sales : 0}}</p>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card ext-margin">
                <div class="card-header">
                    <div class="card-title">30 Days Profit</div>
                </div>
                <div class="card-body">
                    <p class="text-center text-lg">${{$last30DaysProfit}}</p>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card ext-margin">
                <div class="card-header">
                    <div class="card-title">Monthly Totals</div>
                </div>
                <div class="card-body cstm-card">
                    @forelse($coursesSalesMonthly as $key => $val)
                        <div class="d-flex justify-content-between mb-2">
                            <p>{{$val['month']}}</p>
                            <p class="badge badge-success">${{$val['sale']}}</p>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    {{-- End Admin Dashboard --}}
@stop
@push('scripts')
@endpush