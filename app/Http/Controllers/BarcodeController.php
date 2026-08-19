<?php

namespace App\Http\Controllers;

use App\Models\Tools;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeController extends Controller
{
    public function preview(Request $request)
    {
        $query = Tools::select('kode_material', 'deskripsi', 'spesifikasi')
            ->distinct()
            ->orderBy('kode_material', 'asc');

        if ($request->filled('search')) {
            // pisahkan input berdasarkan koma
            $keywords = explode(',', $request->search);

            // Buang yang kosong
            $keywords = array_filter($keywords);

            $query->whereIn('kode_material', $keywords);
        }
        $items = $query->get();
        $generator = new BarcodeGeneratorPNG();
        foreach ($items as $item) {
            $barcode = base64_encode(
                $generator->getBarcode(
                    $item->kode_material,
                    $generator::TYPE_CODE_128
                )
            );
            $item->barcode_base64 = $barcode;
        }
        return view('pages.barcode.preview', compact('items'));
    }
}
