<?php

namespace App\Http\Controllers;

use App\Models\DaftarKodeMaterial;
use App\Models\Departemen;
use App\Models\DetailKerusakanTool;
use App\Models\DistribusiTool;
use App\Models\MaterialTransferPosting;
use App\Models\PenguranganTool;
use App\Models\Posisi_CT;
use App\Models\Project;
use App\Models\PurchaseRequisition;
use App\Models\ToolProjectStock;
use App\Models\Tools;
use App\Models\ToolStockBebas;
use App\Models\UpdateStockTool;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {

        return view('pages.dashboard');
    }
}
