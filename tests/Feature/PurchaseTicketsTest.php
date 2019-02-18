<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    protected $paymentGateway;

    protected function setUp()
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class , $this->paymentGateway);
    }


    public function test_customer_can_purchase_concert_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create([
            'ticket_price' => 3250
        ]);

        $concert->addTickets(5);

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

    public function test_cannot_purchase_tickets_to_an_unpublished_concert()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();

        $response = $this->orderTickets($concert , [
            'email' => 'test@test.ru',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(404);

        $this->assertEquals(0 , $concert->orders()->count());

        $this->assertEquals(0 , $this->paymentGateway->totalCharges());
    }

    public function test_cannot_purchase_more_tickets_than_remain()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $concert->addTickets(2);

        $response = $this->orderTickets($concert , [
            'email' => 'test@test.ru',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(422);

        $order = $concert->orders()->where('email' , 'test@test.tu')->first();

        $this->assertNull($order);

        $this->assertEquals( 0 , $this->paymentGateway->totalCharges());

        $this->assertEquals( 2 , $concert->ticketsRemaining());
    }

    public function test_email_is_required_to_pusrchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert , [
            'email' => '',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $response->assertStatus(422);

        $this->assertArrayHasKey('email' , $response->json()['errors']);
    }

    public function test_email_is_must_be_correct_email()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert , [
            'email' => 'test-email',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->orderAsserts('email' , $response , 422);
    }

    public function test_ticket_quantity_is_required()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert , [
            'email' => 'test@test.ru',
            'ticket_quantity' => '',
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->orderAsserts('ticket_quantity' , $response , 422);
    }

    public function test_ticket_quantity_min_one()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert , [
            'email' => 'test@test.ru',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->orderAsserts('ticket_quantity' , $response , 422);
    }

    public function test_ticket_quantity_is_integer()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert , [
            'email' => 'test@test.ru',
            'ticket_quantity' => 10.2,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->orderAsserts('ticket_quantity' , $response , 422);
    }

    public function test_payment_token_is_required()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert , [
            'email' => 'test@test.ru',
            'ticket_quantity' => 3,
            'payment_token' => ''
        ]);

        $this->orderAsserts('payment_token' , $response , 422);
    }

    public function test_an_order_is_not_created_if_payment_fails()
    {
        $concert = factory(Concert::class)->states('published')->create([
            'ticket_price' => 3250
        ]);

        $response = $this->orderTickets($concert , [
            'email' => 'test@test.ru',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-token'
        ]);

        $response->assertStatus(422);

        $order = $concert->orders()->where('email' , 'test@test.tu')->first();

        $this->assertNull($order);
    }


    private function orderTickets($concert , array $params)
    {
        return $this->json('post' , "/concert/{$concert->id}/orders" , $params);
    }

    private function orderAsserts(string $key , $response , int $statusCode)
    {
        $response->assertStatus($statusCode);

        $this->assertArrayHasKey($key , $response->json()['errors']);
    }
}
