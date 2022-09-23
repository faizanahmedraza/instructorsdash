<?php

namespace Modules\Events\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Events\Entities\AboutUser;
use Modules\Events\Entities\Event;
use Modules\Events\Entities\Guest;
use Modules\Events\Http\Requests\PublicRegisterRequest;
use Modules\Events\Http\Controllers\Payment\PayPal;
use Modules\Events\Http\Controllers\Payment\Stripe;
use Module;
use Modules\Events\Jobs\GuestGreetingJob;
use Modules\Tracklink\Entities\Tracklink;
use Modules\User\Entities\User;

class PublicEventsController extends Controller
{
    public function index($name)
    {
        $user = User::whereRaw('TRIM(LOWER(name)) = ? ',trim(strtolower(str_replace('-', ' ', $name))))->firstOrFail();
        $events = Event::withCount('guests')->where('user_id', $user->id)->where('is_listing', 1)->whereDate('start_date', '>=', date('Y-m-d H:i:s'))->latest()->take(10)->get();
        if (count($events) > 0) {
            foreach ($events as $event) {
                $event['available_seats'] = ($event->quantity == -1) ? 'unlimited' : ($event->quantity - $event->guests_count);
            }
        }
        $about = AboutUser::where('user_id', $user->id)->first();
        if (!empty($about)) {
            $about['description'] = unserialize($about['description']);
        }
        $company = $user->company ?? 'Your Company Name Here';
        return view('events::events.event-landing', compact('events', 'company', 'about','user'));
    }

    public function show(Request $request, $name, $slug)
    {
        $event = Event::with('user')->withCount('guests')->where('short_slug', '=', $slug)->firstOrFail();
        $event['available_seats'] = ($event->quantity == -1) ? 'un limited' : ($event->quantity - $event->guests_count);

        $allowRemoveBrand = true;
        if (Module::find('Saas')) {
            $user = User::findOrFail($event->user_id);
            $allowRemoveBrand = $user->allowRemoveBrand();
        }
        $name = $event->user->name;
        $publishUrl = $event->getPublicUrl($name);
        $eventExpired = $event->eventExpired();

        Tracklink::save_from_request($request, Event::class, $event->id);
        return view('events::event_templates.' . $event->theme . '.main', [
            'event' => $event,
            'allowRemoveBrand' => $allowRemoveBrand,
            'publishUrl' => $publishUrl,
            'eventExpired' => $eventExpired
        ]);
    }

    public function register(PublicRegisterRequest $request, $slug)
    {
        $data = $request->validated();

        $event = Event::where('short_slug', '=', $slug)->firstOrFail();

        $info_items = $event->info_items;

        if (count($info_items) > 0) {
            $info_items['submit'] = [];
            for ($i = 0; $i < count($info_items['name']); $i++) {
                $submit = null;
                if (isset($data['info_item_' . $i])) {
                    $submit = $data['info_item_' . $i];
                }
                array_push($info_items['submit'], $submit);
            }
        }

        $ticket_detail = [null, null];
        $has_tickets = false;
        if (isset($data['ticket']) && !empty($data['ticket'])) {
            $has_tickets = true;
            $ticket_detail = explode(";", $data['ticket']);
        }

        $guest = Guest::create([
            'user_id' => $event->user_id,
            'event_id' => $event->id,
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'info_items' => $info_items,
            'ticket_name' => $ticket_detail[0],
            'ticket_price' => $ticket_detail[1],
            'ticket_currency' => $event->ticket_currency,
        ]);
        // redirect to checkout
        if ($has_tickets) {
            //
            return redirect()->route('events.public.checkout', ['guest_code' => $guest->guest_code]);
        } else {
            GuestGreetingJob::dispatch($event, $guest);
            return redirect()->back()->with('success', $event->noti_register_success);
        }


    }

    public function checkout(Request $request, $guest_code)
    {

        $guest = Guest::where([
            ['guest_code', '=', $guest_code]
        ])->firstOrFail();
        $event = Event::findOrFail($guest->event_id);
        $allowRemoveBrand = true;
        if (Module::find('Saas')) {
            $user = User::findOrFail($event->user_id);
            $allowRemoveBrand = $user->allowRemoveBrand();
        }
        //GuestGreetingJob::dispatch($event, $guest);
        return view('events::event_templates.' . $event->theme . '.checkout', [
            'event' => $event,
            'guest' => $guest,
            'allowRemoveBrand' => $allowRemoveBrand,
            'publishUrl' => $event->getPublicUrl(),
            'eventExpired' => $event->eventExpired(),
        ]);
    }

    public function submitCheckout(Request $request, $guest_code)
    {
        {
            // form checkout with payment method

            $payment_method = "";

            if ($request->has('payment_method')) {

                $type = ['paypal', 'bank_transfer', 'stripe'];
                if (!in_array($request->payment_method, $type)) {
                    return response()->json(['error' => __("Not found type payment")]);
                }

                $payment_method = $request->payment_method;

            } else
                return response()->json(['error' => __("Not found payment method or type")]);
        }
        $guest = Guest::where([
            ['guest_code', '=', $guest_code]
        ])->firstOrFail();

        if (!$guest) {
            return response()->json(['error' => __("Not found guest")]);
        }

        $event = Event::find($guest->event_id);
        if (!$event) {
            return response()->json(['error' => __("Not found event")]);
        }
        $user = $event->user;

        // check exits key Payment
        if ($payment_method == 'paypal') {

            $paypal_keys = ['PAYPAL_CLIENT_ID', 'PAYPAL_SECRET'];
            $check_key_payment = checkIssetAndNotEmptyKeys($user->settings, $paypal_keys);
            if (!$check_key_payment) {
                return response()->json(['error' => __("You need config setting payment PayPal in /accountsettings")]);
            }
        }

        if ($payment_method == 'stripe') {

            $stripe_keys = ['STRIPE_KEY', 'STRIPE_SECRET'];
            $check_key_payment = checkIssetAndNotEmptyKeys($user->settings, $stripe_keys);
            if (!$check_key_payment) {
                return response()->json(['error' => __("You need config setting payment STRIPE in /accountsettings")]);
            }
        }


        // get field if exits form
        $fields_request = array_keys($request->all());

        $fields_expect = ['_token', 'payment_method'];
        foreach ($fields_expect as $item) {
            if (($item = array_search($item, $fields_request)) !== false) {
                unset($fields_request[$item]);
            }
        }
        $fields_request = array_unique($fields_request);
        $field_values = array();

        if (count($fields_request) > 0) {
            foreach ($fields_request as $key) {
                $field_values[$key] = $request->input($key);
            }
        }
        $data = $field_values;
        $data['gateway'] = $payment_method;
        $guest->update($data);

        switch ($payment_method) {

            case 'stripe':

                return (new Stripe)->gateway_purchase($request, $event, $guest);
                break;

            case 'paypal':

                return (new PayPal)->gateway_purchase($request, $event, $guest);
                break;

            case 'bank_transfer':

                GuestGreetingJob::dispatch($event, $guest); //send mail

                $request->session()->flash('success', $event->noti_register_success);

                $data_return = [
                    'success' => $event->noti_register_success,
                    'redirect_url' => $event->getPublicUrl()
                ];
                return response()->json($data_return);
                break;
            default:
                return response()->json(['error' => __("Unsupported payment gateway")]);
                break;
        }

    }

    public function gateway_return(Request $request, Event $event, Guest $guest)
    {

        switch ($guest->gateway) {

            case 'stripe':

                return (new Stripe)->gateway_return($request, $event, $guest);

                break;

            case 'paypal':

                return (new PayPal)->gateway_return($request, $event, $guest);

                break;
            default:
                return response()->json(['error' => __("Unsupported payment gateway")]);
                break;
        }

    }

    public function gateway_cancel(Request $request, Event $event, Guest $guest)
    {
        if ($event)
            return redirect()->to($guest->getCheckoutUrl());

        abort(404);
    }

    public function gateway_notify(Request $request, $gateway)
    {
        switch ($gateway) {

            case 'stripe':

                return (new Stripe)->gateway_notify($request);

                break;

            case 'paypal':

                return (new PayPal)->gateway_notify($request);

                break;

            default:

                return response()->json(['error' => __("Unsupported payment gateway")]);

                break;
        }
    }

    public function checkin(Request $request, $uuid)
    {
        $guest = Guest::where([
            ['user_id', '=', auth()->user()->id],
            ['guest_code', '=', $uuid]
        ])->first();

        if (!$guest) return redirect()->route('guests.index')->with('error', __('QR Code not exists!'));
        if ($guest->status == 'joined')
            return redirect()->route('guests.index')->with('error', $guest->email . " " . __('joined'));

        $guest->status = 'joined';
        $guest->save();
        return redirect()->route('guests.index')->with('success', $guest->email . " " . __('check-in success'));
    }
}
