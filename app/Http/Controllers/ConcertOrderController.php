<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Http\Request;

class ConcertOrderController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store(int $concertId)
    {
        $concert = Concert::published()->findOrFail($concertId);

        $this->validate(\request(), [
            'email' => ['required', 'email'],
            'ticket_quantity' => ['required', 'integer', 'min:1'],
            'payment_token' => ['required']
        ]);

        $ticketQuantity = request('ticket_quantity');

        $amount = $ticketQuantity * $concert->ticket_price;

        $order = $concert->orderTickets(request('email'), $ticketQuantity);

        $this->paymentGateway->charge($amount, request('payment_token'));

        return response()->json($order, 201);
    }
}
