<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckSheetController extends Controller
{
    public function index()
    {
        return view('check-sheet.index');
    }
}
