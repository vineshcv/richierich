<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, Order>  $orders
     */
    public function __construct(public Collection $orders)
    {
    }

    public function envelope(): Envelope
    {
        $order = $this->orders->first();
        $name = $order?->customer_name ?: 'a customer';
        $replyTo = [];
        $email = $order?->email;
        if (is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $replyTo[] = new Address($email, $order->customer_name ?: null);
        }

        return new Envelope(
            from: new Address((string) config('mail.from.address'), 'Richie Rich'),
            subject: 'New Richierich order from '.$name,
            replyTo: $replyTo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.order-placed',
        );
    }
}
