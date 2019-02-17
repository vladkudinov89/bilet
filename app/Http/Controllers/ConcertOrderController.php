<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Concert;
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

        $this->paymentGateway->charge($amount, request('payment_token'));

        $order = $concert->orderTickets(request('email'), $ticketQuantity);

        return response()->json([], 201);
    }
}
