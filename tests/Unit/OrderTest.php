<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    public function test_tickets_are_released_when_an_order_is_cancelled()
    {
        $concert = factory(Concert::class)->create();

        $concert->addTickets(10);

        $order = $concert->orderTickets('test@test.ru' , 5);

        $this->assertEquals(5, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(10 , $concert->ticketsRemaining());

        $this->assertNull(Order::find($order->id));
    }
}