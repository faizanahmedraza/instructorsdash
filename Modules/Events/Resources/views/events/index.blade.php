@extends('core::layouts.app')
@section('title', __('Events'))
@push('head')
    <style>
        .copyButton {
            position:absolute;
            top:5px;
            right:5px;
            font-size:.9rem;
            padding:.15rem;
            background-color:#828282;
            color:1e1e1e;
            border:ridge 1px #7b7b7c;
            border-radius:5px;
            text-shadow:#c4c4c4 0 0 2px;
        }

        .copyButton:hover{
            cursor:pointer;
            background-color:#bcbabb;
        }
    </style>
@endpush
@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <div class="d-flex flex-column mb-2">
            <div class="d-flex justify-content-start">
                <h1 class="h3 text-gray-800">@lang('Events')</h1>
                <a href="#" data-toggle="modal" data-target="#iframeEventModal"
                   class="btn btn-info ml-2 mb-2">@lang('Iframe Code Copier')</a>
            </div>
            <a href="{{ route('all-events.index',['name' => getSlugName(auth()->user()->name)]) }}" target="_blank"
               title="Events Landing Page">{{ route('all-events.index',['name' => getSlugName(auth()->user()->name)]) }}</a>
        </div>
        <div class="ml-auto d-sm-flex">
            <form method="get" action="" class="navbar-search mr-4">
                <div class="input-group">
                    <input type="text" name="query" value="{{ \Request::get('query', '') }}"
                           class="form-control bg-light border-0 small" placeholder="@lang('Search')"
                           aria-label="Search" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @if($events->count() > 0)
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <table class="table card-table table-vcenter text-nowrap">
                        <thead class="thead-dark">
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('URL')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Register end at')</th>
                            <th>@lang('Seats')</th>
                            <th>@lang('Registered')</th>
                            <th>@lang('Actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($events as $event)
                            <tr>
                                <td>
                                    {{ $event->name }}
                                </td>
                                <td>
                                    <a href="{{ $event->getPublicUrl() }}" target="_blank"
                                       title="{{ $event->name }}">{{ $event->getPublicUrl() }}</a>
                                </td>
                                <td>
                                    @php
                                        $tmp_type = '';
                                        switch($event->type){
                                            case 'ONLINE':
                                                $tmp_type = __('Online');
                                                break;
                                            case 'OFFLINE':
                                                $tmp_type = __('Offline');
                                                break;
                                            default:
                                                $tmp_type = '';
                                                break;
                                        }
                                    @endphp
                                    {{ $tmp_type }}
                                </td>
                                <td>
                                    @isset($event->register_end_date)
                                        {{ $event->register_end_date->format('Y-m-d H:i:s') }}
                                    @endisset
                                </td>
                                <td>
                                    @if($event->quantity == -1)
                                        unlimited
                                    @else
                                        {{  $event->available_seats.'/'.$event->quantity }}
                                    @endif
                                </td>
                                <td>
                                    {{$event->guests_count}}
                                </td>
                                <td>
                                    <a href="{{ route('events.edit', ['id' => $event->id]) }}"
                                       class="btn btn-sm btn-primary">@lang('Edit')</a>
                                    <a href="{{ route('tracklink.show', ['target_class' => 'event', 'target_id' => $event->id]) }}"
                                       class="btn btn-sm btn-success">@lang('Statistics')</a>
                                    <form class="d-inline-block form-delete"
                                          action="{{ route('events.delete', ['id' => $event->id]) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-danger btn-sm btn-delete">@lang('Delete')</button>
                                    </form>
                                    <br/>
                                    <a href="{{ route('guests.index', ['event' => $event->name]) }}"
                                       class="btn btn-sm btn-secondary mt-1">@lang('Students')</a>


                                    <a href="#" data-toggle="modal" data-target="#myModal"
                                       class="btn btn-sm btn-info mt-1">@lang('Dublicate Event')</a>

                                    <a href="{{ route('events.sales-review', ['id' => $event->id]) }}"
                                       class="btn btn-sm btn-info mt-1">@lang('Sales')</a>

                                    <!-- Trigger the modal with a button -->
                                    <!--<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>-->

                                    <!-- Modal -->
                                    <div id="myModal" class="modal fade" role="dialog">
                                        <div class="modal-dialog">
                                            <form class="d-inline-block"
                                                  action="{{ route('events.copy', ['id' => $event->id]) }}"
                                                  method="post">
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <!--<div class="modal-header">-->
                                                    <!--  <button type="button" class="close" data-dismiss="modal">&times;</button>-->
                                                    <!--  <h4 class="modal-title"></h4>-->
                                                    <!--</div>-->
                                                    <div class="modal-body">


                                                        @csrf
                                                        <label>How many copies of this event you want to make?</label>
                                                        <input class="form-control" type="number" value=1 name="number"
                                                               required>


                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Create</button>
                                                        <button type="button" class="btn btn-warning"
                                                                data-dismiss="modal">Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>

                                        </div>
                                    </div>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $events->appends( Request::all() )->links() }}
                </div>
            </div>
        </div>
    @endif
    @if($events->count() == 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center">
                    <div class="error mx-auto mb-3"><i class="fas fa-calendar-day"></i></div>
                    <p class="lead text-gray-800">@lang('Not Found')</p>
                    <p class="text-gray-500">@lang("You don't have any event")</p>
                </div>
            </div>
        </div>
    @endif
    @include('events::events.iframe-modal')
@stop

@push('scripts')
    <script>
    </script>
@endpush