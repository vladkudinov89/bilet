<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTicketsTest extends TestCase
{
    protected $paymentGateway;

    protected function setUp()
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class , $this->paymentGateway);
    }

    use DatabaseMigrations;


    public function test_customer_can_purchase_concert_tickets()
    {
        $concert = factory(Concert::class)->create([
            'ticket_price' => 3250
        ]);

        $response = $this->json('post' , "/concert/{$concert->id}/orders" , [
            'email' => 'test@test.tu',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(201);

        $this->assertEquals(9750 , $this->paymentGateway->totalCharges());


        $order = $concert->orders()->where('email' , 'test@test.tu')->first();

        $this->assertNotNull($order);

        $this->assertEquals(3 , $order->tickets()->count());
    }

    public function test_email_is_required_to_pusrchase_tickets()
    {
        $concert = factory(Concert::class)->create();

        $response = $this->json('post' , "/concert/{$concert->id}/orders" , [
            'email' => '',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(422);

        $this->assertArrayHasKey('email' , $response->json()['errors']);
    }


}
