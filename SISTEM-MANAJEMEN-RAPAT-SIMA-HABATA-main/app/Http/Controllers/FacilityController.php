<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index()
    {
        return Facility::all();
    }

    public function store(Request $request)
    {
        return Facility::create($request->all());
    }
}
