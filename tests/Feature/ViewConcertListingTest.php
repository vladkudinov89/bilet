<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewConcertListingTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function test_for_travis()
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        $response->assertSee('Laravel');

        $response->assertDontSee('Home2');

        $response->assertDontSee('Home3');
    }


}
