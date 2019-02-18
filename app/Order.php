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

    public function cancel()
    {
        foreach ($this->tickets as $ticket) {
            $ticket->update(['order_id' => null]);
        }

        $this->delete();
    }
}
