<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use App\Reservation;
use App\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    public function test_converting_to_an_array()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200]);

        $concert->addTickets(5);

        $order = $concert->orderTickets('test@test.ru' , 5);

        $result = $order->toArray();

        $this->assertEquals([
            'email' => 'test@test.ru',
            'ticket_quantity' => 5,
            'amount' => 6000
        ] , $result);
    }

    public function test_creating_an_order_from_reservation()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200]);
        $tickets = factory(Ticket::class , 3)->create(['concert_id' => $concert->id]);

        $reservation = new Reservation($tickets , 'test@test.ru');

        $order = Order::fromReservation($reservation);

        $this->assertEquals('test@test.ru' , $order->email);

        $this->assertEquals(3 , $order->ticketQuantity());

        $this->assertEquals(3600 , $order->amount);
    }


    public function test_creating_an_order_from_tickets_and_email_and_amount()
    {
        $concert = factory(Concert::class)->create()->addTickets(5);

        $this->assertEquals(5 , $concert->ticketsRemaining());

        $order = Order::forTickets($concert->findTickets(3) , 'test@test.ru' , 3600);

        $this->assertEquals('test@test.ru' , $order->email);

        $this->assertEquals(3 , $order->ticketQuantity());

        $this->assertEquals(3600 , $order->amount);

        $this->assertEquals(2 , $concert->ticketsRemaining());
    }


}
