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
    <meta itemprop="image" content="@if($event->social_image){{ config('app.url') }}/storage/{{ $event->social_image }}@endif">
    <!-- Facebook Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{$event->social_title}}">
    <meta property="og:description" content="{{$event->social_description}}">
    <meta property="og:image" content="@if($event->social_image){{ config('app.url') }}/storage/{{ $event->social_image }}@endif">
    <meta property="og:url" content="{{ $publishUrl }}">
    
    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{$event->social_title}}">
    <meta name="twitter:description" content="{{$event->social_description}}">
    <meta name="twitter:image" content="@if($event->social_image){{ config('app.url') }}/storage/{{ $event->social_image }}@endif">
    @if($event->favicon)
    <link rel="icon" href="{{ config('app.url') }}/storage/{{ $event->favicon }}" type="image/png">
    @else
	<link rel="icon" href="{{ asset(config('app.logo_favicon'))}}" type="image/png">
    @endif

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('modules/events/event_templates/strong/lib.css') }}">
	<link rel="stylesheet" href="{{ asset('modules/events/event_templates/strong/template.css') }}">

    <link href="https://fonts.googleapis.com/css?family={{ $event->font_family }}&display=swap" rel="stylesheet">
    @php
         $theme_color = "#2f1c8e";
         if($event->theme_color && $event->theme_color != "#000000"){
            $theme_color = $event->theme_color;
         }
         $background_theme_url = asset('modules/events/event_templates/strong/images/background.png');
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
    <header id="header">
        <div class="flex-container-wrapper"> <!-- IE fix for vertical alignment in flex box -->
            <div class="header-content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h1>{{ $event->name }}</h1>
                            <p class="join-us">{{ $event->tagline }}</p>
                            <a class="button transparent popup-with-move-anim-form" href="#form-popup">@lang('REGISTER')</a>
                        </div>
                    </div> <!-- end of row -->
                </div> <!-- end of container -->
                <svg id="svg-header-bottom" data-name="svg-header-bottom" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 100" preserveAspectRatio="xMinYMax"><defs></defs><title>header-bottom</title><polygon class="header-bottom" points="1920 100 0 100 1920 0 1920 100"></polygon></svg>
            </div> <!-- end of header-content -->
        </div> <!-- end of IE vertical alignment fix -->
    </header>
    <!-- end of header -->

    <!-- end of popup Form -->
    
    <div id="form-popup" class="zoom-anim-dialog mfp-hide">
        <div class="row">
            <div class="col-md-12">
                @if(!$eventExpired)
                <h3><span>Registration</span> Form</h3>
                <!-- Registration Form -->
                <form action="{{ route('events.public.register', ['slug' => $event->short_slug]) }}" method="post" class="form-register">
                    @csrf
                   
                    <div class="form-group">
                        <input type="text" name="fullname" value="{{ old('fullname', '') }}" placeholder="Fullname *" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <input type="email" name="email" value="{{ old('email', '') }}" placeholder="Email *" class="form-control" required>
                    </div>
                    @if(count($event->ticket_items) > 0)
                    <div class="form-group">
                        <select name="ticket" class="form-control" placeholder="Tickets*" required>
                            @php 
                                $tmp_items = $event->ticket_items;
                                $tmp_len = count($tmp_items['name']);
                            @endphp
                            @for($tmp_index = 0; $tmp_index < $tmp_len; $tmp_index++)
                                <option value="{{ $tmp_items['name'][$tmp_index] }};{{ $tmp_items['price'][$tmp_index] }};{{ $tmp_items['currency'][$tmp_index] ?? '' }}">{{ $tmp_items['name'][$tmp_index] }} - {{ $tmp_items['price'][$tmp_index] }} {{ $tmp_items['currency'][$tmp_index] ?? '' }}</option>
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
                                    <div class="form-group">
                                        <input type="text" name="info_item_{{ $tmp_index }}" placeholder="{{ $tmp_items['name'][$tmp_index] }} @if($tmp_items['is_required'][$tmp_index] == '1') * @endif" 
                                        class="form-control" value="{{ old('info_item_' . $tmp_index, '') }}" @if($tmp_items['is_required'][$tmp_index] == '1') required @endif >
                                    </div>
                                    @break
                                @case('textarea')
                                    <div class="form-group">
                                        <textarea name="info_item_{{ $tmp_index }}" class="form-control" placeholder="{{ $tmp_items['name'][$tmp_index] }} @if($tmp_items['is_required'][$tmp_index] == '1') * @endif"  rows="3" @if($tmp_items['is_required'][$tmp_index] == '1') required @endif >{{ old('info_item_' . $tmp_index, '') }}</textarea>
                                    </div>
                                    @break

                                @case('select')
                                    <div class="form-group">
                                        <select name="info_item_{{ $tmp_index }}" placeholder="{{ $tmp_items['name'][$tmp_index] }} @if($tmp_items['is_required'][$tmp_index] == '1') * @endif"  class="form-control" @if($tmp_items['is_required'][$tmp_index] == '1') required @endif >
                                            @foreach($tmp_values as $tmp_value)
                                                <option value="{{ $tmp_value }}" @if(old('info_item_' . $tmp_index, null) == $tmp_value) selected @endif>{{ $tmp_value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @break

                                @default
                            @endswitch
                        @endfor
                    @endif
                    <div class="form-group register-button">
                        <button type="submit" class="disabled">@lang('REGISTER')</button>
                    </div>
                </form>
                @else
                    <h1>@lang('Event has expired!')</h1>
                @endif
            </div>
        </div>
    </div>
    <!-- end of popup form -->
    <div id="subheader">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <!-- Countdown timer -->
                    <div class="countdown text-center disabled">
                        <span class="clock"></span>
                    </div>
                    <a class="page-scroll button solid popup-with-move-anim-form" href="#form-popup">@lang('REGISTER')</a>
                </div>
            </div> <!-- end of row -->
        </div> <!-- end of container -->
        <svg id="svg-subheader-bottom" data-name="svg-subheader-bottom" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 100" preserveAspectRatio="xMinYMax"><defs></defs><title>subheader-bottom</title><polygon class="subheader-bottom" points="1920 100 0 100 1920 0 1920 100"></polygon></svg>
    </div>
     <!-- Description -->
     <div id="description" class="cards-2">
        <div class="container">
            <h2 class="h2"><span class="red">@lang('DESCRIPTION')</span></h2>
            {!! $event->description !!}
        </div> 
    </div> 
    <div id="subheader">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <!-- Countdown timer -->
                    <div class="countdown text-center disabled">
                        <span class="clock"></span>
                    </div>
                    <a class="page-scroll button solid popup-with-move-anim-form" href="#form-popup">@lang('REGISTER')</a>
                </div>
            </div> 
        </div>
    </div>
    @if($event->type == "OFFLINE" && !empty($event->address))
    <div id="description" class="mb-4">
        <div class="container">
            <h2 class="h2"><span class="red">@lang('Event Location')</span></h2>
            <div class="map-responsive mt-4">
                <iframe src="https://maps.google.it/maps?q={{urlencode(strip_tags($event->address))}}&output=embed" allowfullscreen frameBorder="0"></iframe>
            </div>
        </div>
    </div>
    @endif
    <!-- end of description -->
    @if(!$allowRemoveBrand)
    <div class="action_footer">
        <a href="{{ config('app.url') }}" class="cd-top">
            @lang('Powered by') {{ config('app.name') }}
        </a>
    </div>
    @endif
	<!-- Scripts -->
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
    <script src="{{ asset('modules/events/event_templates/strong/lib.js') }}"></script>
	<script src="{{ asset('modules/events/event_templates/strong/template.js') }}"></script>
</body>
</html>