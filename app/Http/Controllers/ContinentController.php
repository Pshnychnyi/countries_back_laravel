<?php

namespace App\Http\Controllers;

use App\Models\Continent;
use Illuminate\Http\Request;

class ContinentController extends Controller
{

    public function continents() {
        return Continent::all();
    }
}
