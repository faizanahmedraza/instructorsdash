<?php

namespace Modules\Events\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Events\Entities\Event;
use Modules\Events\Entities\Guest;

class GuestGreetingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event;
    protected $guest;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Event $event, Guest $guest)
    {
        $this->event = $event;
        $this->guest = $guest;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $event = $this->event;
        $guest = $this->guest;

        Mail::send([], [], function ($message) use ($event, $guest) {
            $body = $event->email_content;

            $body = str_replace('%event_name%', $event->name, $body);
            $body = str_replace('%event_description%', $event->description, $body);
            $body = str_replace('%guest_fullname%', $guest->fullname, $body);
            $body = str_replace('%guest_email%', $guest->email, $body);
            $body = str_replace('%guest_ticket_name%', $guest->ticket_name, $body);
            $body = str_replace('%guest_ticket_price%', $guest->ticket_price, $body);
            $body = str_replace('%guest_ticket_currency%', $guest->ticket_currency, $body);

            $body = str_replace('%event_address%', $event->address, $body);
            if ($event->start_date)
                $body = str_replace('%event_start_date%', $event->start_date->format('Y-m-d H:i:s'), $body);

            $body = str_replace('%qr_code%', $guest->qr_code_image, $body);
            $sender_address = config('mail.from.address');
            $sender_name = config('mail.from.name');

            if ($event->email_sender_email) {
                $sender_address = $event->email_sender_email;
            }
            if ($event->email_sender_name) {
                $sender_name = $event->email_sender_name;
            }


            $message
                ->from($address = $sender_address, $name = $sender_name)
                ->to($guest->email)
                ->subject($event->email_subject)
                ->setBody($body, 'text/html');

        });
    }
}
