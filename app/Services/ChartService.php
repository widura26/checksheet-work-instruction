<?php

namespace App\Services;

use App\Models\BillOfMaterial;
use App\Models\SapDataset;

class ChartService {

    public function getPurchaseRequistionData(){
        $belumPR = SapDataset::where('pr_number', null)->where('status', 'valid')->get();
        $sudahPR = SapDataset::where('pr_number', '!=', '')->where('status', 'valid')->get();

        return [
            'belum_pr' => count($belumPR),
            'sudah_pr' => count($sudahPR),
        ];
    }

    public function getPurchaseOrderData(){
        $belumPO = SapDataset::where('po_number', '')->where('status', 'valid')->get();
        $sudahPO = SapDataset::where('po_number', '!=', '')->where('status', 'valid')->get();

        return [
            'belum_po' => count($belumPO),
            'sudah_po' => count($sudahPO),
        ];
    }

    public function getBompr(){
        $boms = BillOfMaterial::select('name')->get();
        $prs = SapDataset::select('id')->where('pr_number', '!=', '')->where('status', 'valid')->get();

        return [
            'bom' => count($boms),
            'pr' => count($prs)
        ];
    }

    public function getPrPo(){
        $prs = SapDataset::select('id')->where('pr_number', '!=', '')->where('status', 'valid')->get();
        $pos = SapDataset::select('id')->where('po_number', '!=', '')->where('status', 'valid')->get();

        return [
            'pr' => count($prs),
            'pos' => count($pos)
        ];
    }
}

?>
