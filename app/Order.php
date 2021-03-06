<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $guarded = [];

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function ticketQuantity()
    {
        return $this->tickets()->count();
    }

    public function toArray()
    {
        return [
            'email' => $this->email,
            'ticket_quantity' => $this->ticketQuantity(),
            'amount' => $this->amount,
        ];
    }

    public static function forTickets($tickets, $email, $amount = null)
    {
        $order = self::create([
            'email' => $email,
            'amount' => $amount
        ]);

        foreach ($tickets as $ticket) {

            $order->tickets()->save($ticket);
        }

        return $order;
    }
}
