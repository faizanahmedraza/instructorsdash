<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @if(!$event->seo_enable)
        <meta name="robots" content="noindex">
    @endif
    <title>@lang('Checkout') @if(!empty($event->seo_title)) {{$event->seo_title}} @else {{$event->name}} @endif</title>
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
    <link rel="stylesheet" href="{{ asset('modules/events/event_templates/lucky/checkout.css') }}">
    <script src="{{ asset('modules/events/event_templates/lucky/checkout.js') }}"></script>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <div class="container">
        <div class="py-5 text-center">
            <h2>@lang('Checkout') - {{ $event->name }}</h2>
            <p class="lead">{{ $event->tagline }}</p>
        </div>
        <div class="row">
            <div class="col-md-4 order-md-2 mb-4">
                <h4 class="d-flex justify-content-between align-items-center mb-5">
                    <span class="text-muted">@lang('Ticket information')</span>
                </h4>
                <ul class="list-group mb-3 sticky-top">
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                            <h6 class="my-0">{{ $guest->ticket_name }}</h6>
                            <small class="text-muted">{{ $guest->ticket_name }}</small>
                        </div>
                        <span class="text-muted">{{ $guest->ticket_price }} {{ $guest->ticket_currency }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <h6 class="my-0">@lang('Total')</h6>
                        <strong>{{ $guest->ticket_price }} {{ $guest->ticket_currency }}</strong>
                    </li>
                </ul>
            </div>
            <div class="col-md-8 order-md-1">
                <h4 class="mb-3">@lang('Ticket recipient')</h4>
                <form id="form-checkout-event" class="needs-validation" novalidate="">
                    <div class="mb-3">
                        <label for="username">@lang('Fullname')*</label>
                        <input type="text" name="fullname" value="{{ $guest->fullname }}" placeholder="@lang('Fullname')" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email">@lang('Email')*</label>
                        <input type="email" name="email" value="{{ $guest->email }}" placeholder="@lang('Email')" class="form-control" required>
                    </div>
                    <h4 class="mb-3">@lang('Payment')</h4>
                    <div class="d-block my-3">
                        <div class="custom-control custom-radio">
                            <input id="stripe" name="payment_method" value="stripe" type="radio" class="custom-control-input" checked="" required="">
                            <label class="custom-control-label" for="stripe">@lang('Stripe')</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input id="paypal" name="payment_method" value="paypal" type="radio" class="custom-control-input" required="">
                            <label class="custom-control-label" for="paypal">@lang('PayPal')</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input id="bank_transfer" name="payment_method" value="bank_transfer" type="radio" class="custom-control-input" required="">
                            <label class="custom-control-label" for="bank_transfer">@lang('Bank transfer')</label>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Continue to checkout')</button>
                </form>
            </div>
        </div>
        <footer class="my-5 pt-5 text-muted text-center text-small">
            <p class="mb-1">© {{ date('Y') }}</p>
        </footer>
    </div>
</body>
<style>
    .container {
        max-width: 960px;
    }
    .lh-condensed { line-height: 1.25; }
</style>

<script>
    window._orderLink = "{{ url('/')."/e/submit-checkout/".$guest->guest_code }}";
    window._token = "{{ csrf_token() }}";
    var guest_code = "{{ $guest->guest_code }}";
</script>
<script>
(function () {
    'use strict'
    const functionFormPaymentSubmit = function() {
       
        var form = $(this);
        var btn = form.find("button[type=submit]:focus" );

        var payment_method = form.find("[name='payment_method']:checked").val();
        
        var list_payment_method = ['stripe','paypal','bank_transfer'];

        var check_payment_method = list_payment_method.includes(payment_method);
        if (!check_payment_method) {
            alert('Not found payment method please select a payment method');
            return false;
        }
        var values = form.serialize();
        
        values += `&_token=${window._token}`;
      
        var url = window._orderLink.trim();
        $.ajax({
            url: url,
            type: 'POST',
            async: false,
            data: values,
            beforeSend: function() {
                btn.attr("disabled", true);
                btn.after('<smal id="loading-ajax-small">Loading...</small>');
            },
            success: function(data) {
                if($.isEmptyObject(data.error)) {

                    switch (payment_method) {
                        case 'stripe':
                            var stripe = Stripe(`${data.stripe_key}`);
                            stripe.redirectToCheckout({
                                sessionId: `${data.stripe_session_id}`
                            }).then(function (result) {
                                alert(result.error.message);
                                document.location = `${data.cancel_url}`;
                            });
                            break;
                        case 'paypal':
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            }
                        default:
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            }
                            break;
                    }
                }else{
                    alert(data.error);
                    btn.removeAttr("disabled");
                    $('#loading-ajax-small').remove();
                }
                btn.removeAttr("disabled");
                $('#loading-ajax-small').remove();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                btn.removeAttr("disabled");
                $('#loading-ajax-small').remove();
                console.log(xhr);
            }
        });
        btn.removeAttr("disabled");
        $('#loading-ajax-small').remove();
        return false;

    };
    $('form#form-checkout-event').on('submit', functionFormPaymentSubmit);
}())
</script>
</html>