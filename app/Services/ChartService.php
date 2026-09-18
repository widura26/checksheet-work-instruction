<?php

namespace App\Services;

use App\Models\BillOfMaterial;
use App\Models\MaterialPlanning;

class ChartService {

    public function getPurchaseRequistionData(){
        $belumPR = MaterialPlanning::where('pr_number', null)->where('status', 'valid')->get();
        $sudahPR = MaterialPlanning::where('pr_number', '!=', '')->where('status', 'valid')->get();

        return [
            'belum_pr' => count($belumPR),
            'sudah_pr' => count($sudahPR),
        ];
    }

    public function getPurchaseOrderData(){
        $belumPO = MaterialPlanning::where('po_number', '')->where('status', 'valid')->get();
        $sudahPO = MaterialPlanning::where('po_number', '!=', '')->where('status', 'valid')->get();

        return [
            'belum_po' => count($belumPO),
            'sudah_po' => count($sudahPO),
        ];
    }

    public function getBompr(){
        $boms = BillOfMaterial::select('name')->get();
        $prs = MaterialPlanning::select('id')->where('pr_number', '!=', '')->where('status', 'valid')->get();

        return [
            'bom' => count($boms),
            'pr' => count($prs)
        ];
    }
}

?>
