<?php

namespace Tests\Unit;

use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    public function test_can_get_formatted_date()
    {
        $concert = factory(Concert::class)->make([
           'date' => Carbon::parse('2016-12-01 8:00pm')
        ]);

        $this->assertEquals('December 1,2016', $concert->formatted_date);
    }

    public function test_can_get_start_time_formatted()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 17:00:00')
        ]);

        $this->assertEquals('5:00pm' , $concert->formatted_start_time);
    }

    public function test_can_get_ticket_price_format()
    {
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750
        ]);

        $this->assertEquals('67.50' , $concert->ticket_price_in_dollars);
    }

    public function test_concerts_with_a_published_at_date_are_published()
    {
        $publishedConcertA = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);
        $publishedConcertB = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);
        $publishedConcert = factory(Concert::class)->create(['published_at' => null]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($publishedConcert));
    }

    public function test_can_order_concert_tickets()
    {
        $concert = factory(Concert::class)->create();

        $concert->addTickets(50);

        $order = $concert->orderTickets('jane@test.ru' , 3);

        $this->assertEquals('jane@test.ru' , $order->email);

        $this->assertEquals(3 , $order->tickets()->count());
    }

    public function test_can_add_tickets()
    {
        $concert = factory(Concert::class)->create();

        $concert->addTickets(50);

        $this->assertEquals(50 , $concert->ticketsRemaining());
    }

    public function test_tickets_remaining_does_not_include_tickets_assiciated_with_an_order()
    {
        $concert = factory(Concert::class)->create();

        $concert->addTickets(50);

        $concert->orderTickets('jane@test.ru' , 30);

        $this->assertEquals(20 , $concert->ticketsRemaining());
    }

    /**
     *
     * @expectedException NotEnoughTicketsException
     */
    public function test_trying_to_purchase_more_tickets_than_remain_throws_an_exception()
    {
        $this->expectException(NotEnoughTicketsException::class);

        $concert = factory(Concert::class)->create();

        $concert->addTickets(10);

        $concert->orderTickets('jane@test.ru' , 11);

        $order = $concert->orders()->where('email' , 'jane@test.ru')->first();

        $this->assertNull($order);

        $this->assertEquals(10 , $concert->ticketsRemaining());
    }

    public function test_cannot_order_tickets_that_have_already_been_purchase()
    {
        $this->expectException(NotEnoughTicketsException::class);

        $concert = factory(Concert::class)->create();

        $concert->addTickets(10);

        $concert->orderTickets('jane@test.ru' , 8);

        $this->assertEquals(2 , $concert->ticketsRemaining());

        $concert->orderTickets('test@test.ru' , 3);

        $testOrder = $concert->orders()->where('email' , 'test@test.ru')->first();

        $this->assertNull($testOrder);



    }

}
