<?php

namespace App\Http\Controllers;

use App\Concert;
use Illuminate\Http\Request;

class ConcertsController extends Controller
{
    public function show(int $concertId)
    {
        $concert = Concert::published()->findorFail($concertId);

        return view('concerts.show' , ['concert' => $concert]);
    }
}
