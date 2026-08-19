<?php

namespace App\Livewire\TransferPosting;

use App\Exports\MaterialTransferPostingExport;
use App\Models\MaterialTransferPosting;
use App\Models\Project;
use App\Models\ToolProjectStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $cancelReason;
    public $selectedTransferId;
    public $startDate;
    public $endDate;
    public $fromProjectId;
    public $toProjectId;
    public $status;
    public $kodeMaterial;
    public function cancelTransfer($id)
    {
        try {
            DB::beginTransaction();
            $transfer = MaterialTransferPosting::find($id);
            if ($transfer->status === 'RETURNED' || $transfer->status === 'CANCELED' || $transfer->status === 'WRITTEN_OFF' || $transfer->status === 'PARTIAL_RETURN') {
                abort(400, 'Transfer posting sudah dikembalikan atau dibatalkan.');
            }
            $transfer->status = 'CANCELED';
            $transfer->canceled_at = now();
            $transfer->cancel_reason = $this->cancelReason;
            $transfer->save();

            foreach ($transfer->items as $item) {
                $qtyToReturn = $item->qty;

                if ($qtyToReturn > 0) {
                    // kurangi stock di material tujuan
                    $materialDest = ToolProjectStock::where('tool_id', $item->to_tool_id)
                        ->where('project_id', $transfer->to_project_id)
                        ->where('posisi_ct_id', $transfer->to_posisi_ct_id)
                        ->first();
                    if ($materialDest) {
                        $materialDest->qty -= $qtyToReturn;
                        $materialDest->save();
                    }

                    // tambahkan stock di material asal
                    $fromMaterialStock = ToolProjectStock::where('tool_id', $item->from_tool_id)
                        ->where('project_id', $transfer->from_project_id)
                        ->where('posisi_ct_id', $transfer->from_posisi_ct_id)
                        ->first();
                    if ($fromMaterialStock) {
                        $fromMaterialStock->qty += $qtyToReturn;
                        $fromMaterialStock->save();
                    }
                }
            }

            DB::commit();
            $this->reset('cancelReason');
            $this->dispatch('closeCancelModal');
            session()->flash('success', 'Transfer posting Telah di Batalkan');
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', 'Transfer posting Gagal Dibatalkan');
        }
    }

    //tidak perlu dikembalikan
    public function writtenOff($id)
    {
        try {
            DB::beginTransaction();
            $transfer = MaterialTransferPosting::find($id);
            if (! $transfer) {
                abort(404, 'Transfer posting tidak ditemukan.');
            }
            if (in_array($transfer->status, ['RETURNED', 'CANCELED', 'WRITTEN_OFF'])) {
                abort(400, 'Transfer posting sudah dikembalikan, dibatalkan, atau sudah di written off.');
            }
            $transfer->status = 'WRITTEN_OFF';
            $transfer->save();
            DB::commit();
            $this->dispatch('writtenOffSuccess');
            session()->flash('success', 'Transfer posting berhasil tidak di kembalikan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('error saat mengupdate transfer posting', $e->getMessage());
            session()->flash('error', 'Transfer posting gagal di written off ');
        }
    }
    public function resetFilter()
    {
        $this->reset(['startDate', 'endDate', 'fromProjectId', 'toProjectId', 'status', 'kodeMaterial']);
    }
    public function render()
    {
        $query = MaterialTransferPosting::with('fromProject', 'toProject', 'createdBy', 'items.fromTool')
            ->latest();
        if ($this->startDate) {
            $query->whereDate('transfer_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('transfer_date', '<=', $this->endDate);
        }

        if ($this->fromProjectId) {
            $query->where('from_project_id', $this->fromProjectId);
        }

        if ($this->toProjectId) {
            $query->where('to_project_id', $this->toProjectId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }
        if ($this->kodeMaterial) {
            $query->whereHas('items.fromTool', function ($query) {
                $query->where('tools.kode_material', $this->kodeMaterial);
            });
        }
        $transferPostings = $query->paginate(10);

        $projects = Project::all();
        return view('livewire.transfer-posting.index', [
            'transferPostings' => $transferPostings,
            'projects' => $projects,
        ]);
    }
    public function exportToExcel()
    {
        return Excel::download(new MaterialTransferPostingExport(
            $this->startDate,
            $this->endDate,
            $this->fromProjectId,
            $this->toProjectId,
            $this->status,
            $this->kodeMaterial
        ), now()->format('Y-m-d') . '_transfer_posting.xlsx');
    }
}
