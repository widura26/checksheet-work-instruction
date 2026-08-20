<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectCodeController extends Controller
{
    public function index(Request $request){
        return view('project-code.index');
    }
}
