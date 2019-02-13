<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConcertTest extends TestCase
{
//    use DatabaseMigrations;

    use RefreshDatabase;

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


}
