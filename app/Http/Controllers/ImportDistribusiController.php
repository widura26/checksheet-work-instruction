<?php

namespace App\Http\Controllers;

use App\Imports\HistoryDistribusi;
use App\Imports\HistoryDistribusiValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ImportDistribusiController extends Controller
{
    public function importDistribusi(Request $request)
    {
        if (! Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        $file = $request->file('file');
        $validator = new HistoryDistribusiValidation();
        Excel::import($validator, $file);

        // if (!empty($validator->errors)) {
        //     return back()->with([
        //         'import_errors' => $validator->errors,
        //         'message' => 'Import dibatalkan, periksa error berikut.',
        //     ]);
        // }
        Excel::import(new HistoryDistribusi(), $file);
        return redirect()->back()->with([
            'import_errors' => $validator->errors,
            'message' => 'Import selesai.',
        ]);
    }
}
