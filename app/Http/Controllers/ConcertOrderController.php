<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use App\Order;
use App\Reservation;
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

        $tickets = $concert->reserveTickets(request('ticket_quantity'));
        $reservation = new Reservation($tickets);

        $this->paymentGateway->charge($reservation->totalCost(), request('payment_token'));

        $order = Order::forTickets($tickets , request('email') , $reservation->totalCost());

        return response()->json($order, 201);
    }
}
