<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Simulation extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'simulation';
    }
}