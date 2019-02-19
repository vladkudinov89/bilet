<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use App\Order;
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

        $this->validate(request(), [
            'email' => ['required', 'email'],
            'ticket_quantity' => ['required', 'integer', 'min:1'],
            'payment_token' => ['required']
        ]);

        $tickets = $concert->findTickets(\request('ticket_quantity'));

        $this->paymentGateway->charge($tickets->sum('price'), request('payment_token'));

        $order = Order::forTickets($tickets , request('email') , $tickets->sum('price'));

        return response()->json($order, 201);
    }
}
