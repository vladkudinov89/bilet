<?php

namespace Tests\Unit;

use App\Concert;
use App\Reservation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

}
