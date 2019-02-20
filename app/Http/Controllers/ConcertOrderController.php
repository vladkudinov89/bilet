<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use App\Exceptions\PaymentFailedException;
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


        try {
            $reservation = $concert->reserveTickets(request('ticket_quantity') , request('email'));

            $this->paymentGateway->charge($reservation->totalCost(), request('payment_token'));

            $order = $reservation->complete();

            return response()->json($order, 201);

        } catch (PaymentFailedException $e) {

            $reservation->cancel();

            return response()->json([], 422);

        } catch (NotEnoughTicketsException $e) {

            return response()->json([], 422);

        }


    }
}
