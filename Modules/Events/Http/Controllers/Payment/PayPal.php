<?php

namespace Modules\Events\Http\Controllers\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Omnipay\Omnipay;

use Modules\Events\Entities\Event;
use Modules\Events\Entities\Guest;
use Modules\Events\Jobs\GuestGreetingJob;

class PayPal
{
    public function gateway_purchase(Request $request, Event $event, Guest $guest)
    {

        $paypal = Omnipay::create('PayPal_Rest');
        $user = $event->user;

        $paypal->initialize([
            'clientId'  => getValueIfKeyIsset($user->settings,'PAYPAL_CLIENT_ID'),
            'secret'    => getValueIfKeyIsset($user->settings,'PAYPAL_SECRET'),
            'testMode'  => getValueIfKeyIsset($user->settings,'PAYPAL_SANDBOX'),
            'brandName' => $event->name,
        ]);

        try {

            // Send purchase request
            $response = $paypal->purchase([
                'transactionId' => $guest->id,
                'amount'        => $guest->ticket_price,
                'currency'      => $guest->ticket_currency,
                'description'   => $event->name." - ".$guest->ticket_name,
                'cancelUrl'     => route('events.public.checkout.gateway.cancel', [$event, $guest]),
                'returnUrl'     => route('events.public.checkout.gateway.return', [$event, $guest]),
                'notifyUrl'     => route('events.public.checkout.gateway.notify', [$event, $guest]),
            ])->send();

            // Process response
            if ($response->isRedirect()) {
                // Redirect to offsite payment gateway
                return response()->json(['redirect_url'=> $response->getRedirectUrl()]);

            }elseif ($response->isSuccessful()) {

                // Payment was successful
                $guest->reference = $response->getTransactionReference();
                $guest->is_paid   = true;
                $guest->save();

                GuestGreetingJob::dispatch($event, $guest); //send mail
                return redirect()->to($event->getPublicUrl())->with('success', $event->noti_register_success);

            } else {
                // Payment failed
                return response()->json(['error'=> $response->getMessage()]);
            }

        } catch (\Exception $e) {
            return response()->json(['error'=> $e->getMessage()]);
        }
    }

    public function gateway_return(Request $request, Event $event, Guest $guest)
    {
        
        if (!$request->paymentId || !$request->PayerID) {
            # code...
            return response()->json(['error'=> 'Not found paymentId or Payer ID']);
        }
        $user = $event->user;
        $paypal = Omnipay::create('PayPal_Rest');

        $paypal->initialize([
            'clientId'  => getValueIfKeyIsset($user->settings,'PAYPAL_CLIENT_ID'),
            'secret'    => getValueIfKeyIsset($user->settings,'PAYPAL_SECRET'),
            'testMode'  => getValueIfKeyIsset($user->settings,'PAYPAL_SANDBOX'),
            'brandName' => $event->name,
        ]);
        

        try {

            // Complete purchase
            $response = $paypal->completePurchase([
                'transactionId'        => $guest->id,
                'payer_id'             => $request->PayerID,
                'transactionReference' => $request->paymentId,
                'amount'               => $guest->ticket_price,
                'currency'             => $guest->ticket_currency,
                'description'   => $event->name." - ".$guest->ticket_name,
                'cancelUrl'     => route('events.public.checkout.gateway.cancel', [$event, $guest]),
                'returnUrl'     => route('events.public.checkout.gateway.return', [$event, $guest]),
                'notifyUrl'     => route('events.public.checkout.gateway.notify', [$event, $guest]),
            ])->send();

            // Process response
            if ($response->isSuccessful()) {

                // Payment was successful
                $guest->reference = $response->getTransactionReference();
                $guest->is_paid   = true;
                $guest->save();

                GuestGreetingJob::dispatch($event, $guest); //send mail
                return redirect()->to($event->getPublicUrl())->with('success', $event->noti_register_success);


            } else {
                return response()->json(['error'=> $response->getMessage()]);
          
            }
            

        } catch (\Exception $e) {
            return response()->json(['error'=> $e->getMessage()]);
        }
    }

    public function gateway_notify(Request $request)
    {

    }

    public function gateway_cancel(Request $request, LandingpageOrder $payment_order)
    {

    }

}
