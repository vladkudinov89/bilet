<?php

namespace Tests\Unit\Billing;

use App\Billing\StripePaymentGateway;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StripePaymentTest extends TestCase
{
    public function test_charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));

        $token = \Stripe\Token::create([
           "card" => [
               "number" => '4242424242424242',
               "exp_month" => 11,
               "exp_year" => 2020,
               "cvc" => "991"

           ]
        ] , ['api_key' => config('services.stripe.secret')])->id;

        $paymentGateway->charge(2500 , $token);

       $lastCharge = \Stripe\Charge::all(
           ["limit" => 1],
           ["api_key" => config('services.stripe.secret')]
       )['data'][0];

        $this->assertEquals(2500 , $lastCharge->amount);
    }

}
