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

    public function purchaseOrder(){
        $chatService = new ChartService();

        return response()->json([
            "success" => true,
            "jenis_po" => $chatService->getPurchaseOrderData()
        ]);
    }

    public function bomprdifference(){
        $chatService = new ChartService();

        return response()->json([
            "success" => true,
            "bompr" => $chatService->getBompr()
        ]);
    }

    public function poprdifference(){
        $chatService = new ChartService();

        return response()->json([
            "success" => true,
            "popr" => $chatService->getPrPo()
        ]);
    }
}
