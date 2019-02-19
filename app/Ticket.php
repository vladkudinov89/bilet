<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $guarded = [];

    public function scopeAvailable($query)
    {
        return $query->whereNull('order_id')->whereNull('reserved_at');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function getPriceAttribute()
    {
        return $this->concert->ticket_price;
    }

    public function release()
    {
       $this->update(['order_id' => null]);
    }

    public function reserve()
    {
        return $this->update(['reserved_at' => Carbon::now()]);
    }
}
