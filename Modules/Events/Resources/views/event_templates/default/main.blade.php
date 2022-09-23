<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @if(!$event->seo_enable)
        <meta name="robots" content="noindex">
    @endif
    <title>@if(!empty($event->seo_title)) {{$event->seo_title}} @else {{$event->name}} @endif</title>
    <meta name="description" content="{{$event->seo_description}}">
    <meta name="keywords" content="{{$event->seo_keywords}}">
    <!-- Apple Stuff -->
    <link rel="apple-touch-icon" href="{{ config('app.url') }}/storage/{{ $event->favicon }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Title">
    <!-- Google / Search Engine Tags -->
    <meta itemprop="name" content="{{$event->seo_title}}">
    <meta itemprop="description" content="{{$event->seo_description}}">
    <meta itemprop="image"
          content="@if($event->social_image){{ config('app.url') }}/storage/{{ $event->social_image }}@endif">
    <!-- Facebook Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{$event->social_title}}">
    <meta property="og:description" content="{{$event->social_description}}">
    <meta property="og:image"
          content="@if($event->social_image){{ config('app.url') }}/storage/{{ $event->social_image }}@endif">
    <meta property="og:url" content="{{ $publishUrl }}">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{$event->social_title}}">
    <meta name="twitter:description" content="{{$event->social_description}}">
    <meta name="twitter:image"
          content="@if($event->social_image){{ config('app.url') }}/storage/{{ $event->social_image }}@endif">
    @if($event->favicon)
        <link rel="icon" href="{{ config('app.url') }}/storage/{{ $event->favicon }}" type="image/png">
    @else
        <link rel="icon" href="{{ asset(config('app.logo_favicon'))}}" type="image/png">
    @endif

<!-- Styles -->
    <link rel="stylesheet" href="{{ asset('modules/events/event_templates/default/lib.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/events/event_templates/default/template.css') }}">

    <link href="https://fonts.googleapis.com/css?family={{ $event->font_family }}&display=swap" rel="stylesheet">
    @php

        $theme_color = "#316abc";
        if($event->theme_color && $event->theme_color != "#000000"){
           $theme_color = $event->theme_color;
        }
        $background_theme_url = asset('modules/events/event_templates/default/images/background.png');
        if($event->background){
           $background_theme_url = config('app.url'). '/storage/'. $event->background;
        }
        $font_css = explode(':',$event->font_family);
    @endphp
    <style>
        /* variable */
        :root {
            --theme-color: {{ $theme_color }};
            --background-image: url({{ $background_theme_url }});
            --font-family: '{{ $font_css[0] }}';
        }
    </style>
</head>
<body data-spy="scroll">
{{-- Alert --}}
@if($errors->any())
    <div class="alert alert-danger mb-0">
        <ul class="list-unstyled">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('success'))
    <div class="alert alert-success mb-0">
        {!! session('success') !!}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger mb-0">
        {!! session('error') !!}
    </div>
@endif
<!-- Header -->
<header id="header" class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="countdown">
                    <span id="clock"></span>
                </div>
                <h1 class="h1-large">{{ $event->name }}</h1>
                <p class="p-large">{{ $event->tagline }}</p>
                <a class="btn-outline-lg page-scroll" href="#registration">@lang('REGISTER')</a>
                <a class="btn-outline-lg page-scroll" href="#description">@lang('DESCRIPTION')</a>
                <p class="p-large pt-3 mb-0">@lang('Registered Users - '){{ $event->guests_count }}</p>
                <p class="p-large mt-0">@lang('Seats Left - '){{ $event->available_seats }}</p>
            </div>
        </div>
    </div>
</header>
<!-- end of header -->
<!-- Description -->
<div id="description" class="cards-2">
    <div class="container">
        <h2 class="h2"><span class="red">@lang('DESCRIPTION')</span></h2>
        <p>
            {!! $event->description !!}
        </p>
        @if(count($event->ticket_items) > 0)
            <h2 class="h2"><span class="red">@lang('Tickets')</span></h2>
            <table class="table">
                <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">@lang('Type')</th>
                    <th scope="col">@lang('Description')</th>
                    <th scope="col">@lang('Price')</th>
                    <th scope="col">@lang('Currency')</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $tmp_items = $event->ticket_items;
                    $tmp_len = count($tmp_items['name']);
                @endphp
                @for($tmp_index = 0; $tmp_index < $tmp_len; $tmp_index++)
                    <tr>
                        <th scope="row">{{ $tmp_index + 1 }}</th>
                        <td>{{ $tmp_items['name'][$tmp_index] }}</td>
                        <td>{{ $tmp_items['description'][$tmp_index] }}</td>
                        <td>{{ $tmp_items['price'][$tmp_index] }}</td>
                        <td>{{ $tmp_items['currency'][$tmp_index] ?? '' }}</td>
                    </tr>
                @endfor
                </tbody>
            </table>
        @endif
    </div>
</div>
<!-- end of description -->

<!-- Registration -->
<div id="registration" class="form-1 bg-gray" style="clear: both;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <h2 class="h2-heading text-center"><span class="red">@lang('REGISTER')</span> @lang('Form')</h2>
                <!-- Registration Form -->
                @if(!$eventExpired)
                    <div class="form-container">
                        <form action="{{ route('events.public.register', ['slug' => $event->short_slug]) }}"
                              method="post" class="form-register">
                            @csrf

                            <div class="form-group required">
                                <label>@lang('Name')</label>
                                <input type="text" name="fullname" value="{{ old('fullname', '') }}"
                                       class="form-control-input" required>
                            </div>

                            <div class="form-group required">
                                <label>@lang('Email')</label>
                                <input type="email" name="email" value="{{ old('email', '') }}"
                                       class="form-control-input" required>
                            </div>
                            @if(count($event->ticket_items) > 0)
                                <div class="form-group required">
                                    <label>@lang('Tickets')</label>
                                    <select name="ticket" class="form-control-select">
                                        @php
                                            $tmp_items = $event->ticket_items;
                                            $tmp_len = count($tmp_items['name']);
                                        @endphp
                                        @for($tmp_index = 0; $tmp_index < $tmp_len; $tmp_index++)
                                            <option value="{{ $tmp_items['name'][$tmp_index] }};{{ $tmp_items['price'][$tmp_index] }};{{ $tmp_items['currency'][$tmp_index] ?? '' }}">{{ $tmp_items['name'][$tmp_index] }}
                                                - {{ $tmp_items['price'][$tmp_index] }} {{ $tmp_items['currency'][$tmp_index] ?? '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                            @endif
                            @if(count($event->info_items) > 0)
                                @php
                                    $tmp_items = $event->info_items;
                                    $tmp_len = count($tmp_items['name']);
                                @endphp
                                @for($tmp_index = 0; $tmp_index < $tmp_len; $tmp_index++)
                                    @php
                                        $tmp_values = $tmp_items['values'][$tmp_index];
                                        $tmp_values = explode(',', $tmp_values);
                                    @endphp
                                    @switch($tmp_items['data_type'][$tmp_index])
                                        @case('text')
                                        <div class="form-group @if($tmp_items['is_required'][$tmp_index] == '1') required @endif">
                                            <label>{{ $tmp_items['name'][$tmp_index] }}</label>
                                            <input type="text" name="info_item_{{ $tmp_index }}"
                                                   class="form-control-input"
                                                   value="{{ old('info_item_' . $tmp_index, '') }}"
                                                   @if($tmp_items['is_required'][$tmp_index] == '1') required @endif >
                                        </div>
                                        @break
                                        @case('textarea')
                                        <div class="form-group @if($tmp_items['is_required'][$tmp_index] == '1') required @endif">
                                            <label>{{ $tmp_items['name'][$tmp_index] }}</label>
                                            <textarea name="info_item_{{ $tmp_index }}" class="form-control-input"
                                                      rows="3"
                                                      @if($tmp_items['is_required'][$tmp_index] == '1') required @endif >{{ old('info_item_' . $tmp_index, '') }}</textarea>
                                            @error('info_item_' . $tmp_index)
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        @break
                                        @case('select')
                                        <div class="form-group @if($tmp_items['is_required'][$tmp_index] == '1') required @endif">
                                            <label>{{ $tmp_items['name'][$tmp_index] }}</label>
                                            <select name="info_item_{{ $tmp_index }}" class="form-control-select"
                                                    @if($tmp_items['is_required'][$tmp_index] == '1') required @endif >
                                                @foreach($tmp_values as $tmp_value)
                                                    <option value="{{ $tmp_value }}"
                                                            @if(old('info_item_' . $tmp_index, null) == $tmp_value) selected @endif>{{ $tmp_value }}</option>
                                                @endforeach
                                            </select>
                                            @error('info_item_' . $tmp_index)
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        @break
                                        @default
                                    @endswitch
                                @endfor
                            @endif
                            <div class="form-group">
                                <button type="submit" class="form-control-submit-button">@lang('REGISTER')</button>
                            </div>
                        </form>
                    </div>
                @else
                    <h2 class="text-center">@lang('Event has expired!')</h2>
            @endif
            <!-- end of registration form -->
            </div>
        </div>
    </div>
</div>
<!-- end of registration -->
@if($event->type == "OFFLINE" && !empty($event->address))
    <div id="description" class="cards-2">
        <div class="container">
            <h2 class="h2"><span class="red">@lang('Event Location')</span></h2>
            <div class="map-responsive mt-4">
                <iframe src="https://maps.google.it/maps?q={{urlencode(strip_tags($event->address))}}&output=embed"
                        allowfullscreen frameBorder="0"></iframe>
            </div>
        </div>
    </div>
@endif
<!-- Scripts -->
@if(!$allowRemoveBrand)
    <div class="action_footer">
        <a href="{{ config('app.url') }}" class="cd-top">
            @lang('Powered by') {{ config('app.name') }}
        </a>
    </div>
@endif
<script src="{{ asset('modules/events/event_templates/default/lib.js') }}"></script>
<script>
    var register_end_date = `{{ $event->register_end_date->format('Y/m/d H:i:s') }}`;
    var langs = {
        "countTimeDays": "@lang('Days')",
        "countTimeHours": "@lang('Hours')",
        "countTimeMinutes": "@lang('Minutes')",
        "countTimeSeconds": "@lang('Seconds')",
        "eventExpired": "@lang('Event has expired!')",
    };
</script>
<script src="{{ asset('modules/events/event_templates/default/template.js') }}"></script>
</body>
</html>