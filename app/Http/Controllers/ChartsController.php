<?php

namespace App\Http\Controllers;
use App\Services\ChartService;

class ChartsController extends Controller
{
    public function purchaseRequisition(){
        $chatService = new ChartService();

        return response()->json([
            "success" => true,
            "jenis_pr" => $chatService->getPurchaseRequistionData()
        ]);
    }
}
