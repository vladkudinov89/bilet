<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    public function test_can_get_formatted_date()
    {
        $concert = factory(Concert::class)->create([
           'date' => Carbon::parse('2016-12-01 8:00pm')
        ]);

        $this->assertEquals('December 1,2016', $concert->formatted_date);
    }

    public function test_can_get_start_time_formatted()
    {
        $concert = factory(Concert::class)->create([
            'date' => Carbon::parse('2016-12-01 17:00:00')
        ]);

        $this->assertEquals('5:00pm' , $concert->formatted_start_time);
    }


}
