<?php

namespace App\Services;

use App\Models\MaterialPlanning;

class ChartService {

    public function getPurchaseRequistionData(){
        $data = MaterialPlanning::select('');

        return $data;
    }

    public function getPurchaseOrderData(){

    }
}

?>
