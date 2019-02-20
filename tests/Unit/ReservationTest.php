<?php

namespace Tests\Unit;

use App\Concert;
use App\Reservation;
use App\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_calculating_the_total_cost()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(3);

        $tickets = $concert->findTickets(3);

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600 , $reservation->totalCost());
    }

    public function test_retrieving_the_reservation_tickets()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(3);

        $tickets = $concert->findTickets(3);

        $reservation = new Reservation($tickets);

        $this->assertEquals($tickets , $reservation->tickets());

    }


    public function test_reserved_tickets_are_released_a_reservation_is_cancelled()
    {
        $ticket1 = \Mockery::mock(Ticket::class);
        $ticket1->shouldReceive('release')->once();

        $ticket2 = \Mockery::mock(Ticket::class);
        $ticket2->shouldReceive('release')->once();

        $ticket3 = \Mockery::mock(Ticket::class);
        $ticket3->shouldReceive('release')->once();

        $tickets = collect([$ticket1 , $ticket2, $ticket3]);

        $reservation = new Reservation($tickets);

        $reservation->cancel();
    }


}
