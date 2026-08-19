<?php

namespace App\Http\Controllers;

class BarcodeController extends Controller
{
    public function index()
    {
        return view('barcode.index');
    }
}