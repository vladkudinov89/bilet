<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $guarded = [];

    public function scopeAvailable($query)
    {
        return $query->whereNull('order_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
