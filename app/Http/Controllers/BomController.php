<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class BomController extends Controller
{
    public function index(Request $request){
        return view('bill-of-material.index');
    }
}
