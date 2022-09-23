<?php

namespace Modules\Events\Http\Controllers\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Price as StripePrice;
use Stripe\Product as StripeProduct;
use Stripe\Stripe as StripeGateway;
use Stripe\Subscription as StripeSubscription;
use Stripe\Webhook as StripeWebhook;

use Modules\Events\Entities\Event;
use Modules\Events\Entities\Guest;
use Modules\Events\Jobs\GuestGreetingJob;

class Stripe
{
    public function gateway_purchase(Request $request, Event $event, Guest $guest)
    {
        // Set API key
        $user = $event->user;

        StripeGateway::setApiKey(getValueIfKeyIsset($user->settings,'STRIPE_SECRET'));
        
        $stripe_key = getValueIfKeyIsset($user->settings,'STRIPE_KEY');

        try {

            $stripe_session = StripeSession::create([
              'payment_method_types' => ['card'],
              'line_items' => [[
                  'price_data' => [
                    'currency' => $guest->ticket_currency,
                    'product_data' => [
                      'name' => $event->name." - ".$guest->ticket_name,
                    ],
                    'unit_amount' => $guest->total_in_cents,
                  ],
                  'quantity' => 1,
              ]],
              'mode' => 'payment',
              'success_url' => route('events.public.checkout.gateway.return', [$event, $guest])."?session_id={CHECKOUT_SESSION_ID}",
              'cancel_url' => route('events.public.checkout.gateway.cancel', [$event, $guest]),
            ]);

         
            $data_return = [
                'stripe_key' => $stripe_key,
                'page_url' => "#",
                'stripe_session_id' => $stripe_session->id,
            ];

            return response()->json($data_return);


        } catch (\Exception $e) {
            return response()->json(['error'=> $e->getMessage()]);
        }
    }

    public function gateway_return(Request $request, Event $event, Guest $guest)
    {

        if (!$request->session_id) {
            return response()->json(['error'=> 'Not found session_id']);
        }

        // Set API key
        $user = $event->user;
        StripeGateway::setApiKey(getValueIfKeyIsset($user->settings,'STRIPE_SECRET'));
        $stripe_key = getValueIfKeyIsset($user->settings,'STRIPE_KEY');

        try {

            $stripe_session = StripeSession::retrieve($request->session_id);
            // Payment was successful
            if (in_array(strtolower($stripe_session->payment_status), ['paid'])) {

                $guest->reference = $stripe_session->id;
                $guest->is_paid   = true;
                $guest->save();
                
                GuestGreetingJob::dispatch($event, $guest); //send mail
                
                $request->session()->flash('success', $event->noti_register_success);

                return redirect()->to($event->getPublicUrl());
            
            } else {
                return response()->json(['error'=> $stripe_session->getMessage()]);
            }

        } catch (\Exception $e) {

            return response()->json(['error'=> $e->getMessage()]);
        }
    }

    public function gateway_notify(Request $request)
    {

    }

    public function gateway_cancel(Request $request, Event $event, Guest $guest)
    {

    }

}
