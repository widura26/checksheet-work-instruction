<?php

namespace App\Livewire\TransferPosting;

use App\Models\MaterialTransferHistory;
use App\Models\MaterialTransferPosting;
use App\Models\ToolProjectStock;
use App\Models\Tools;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ReturnTransfer extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $showModal = false;

    public $selectedPosting;

    public $returnItems = [];

    public function startReturn($postingId)
    {
        $this->selectedPosting = MaterialTransferPosting::with('items.fromTool')->findOrFail($postingId);
        $this->returnItems = [];
        foreach ($this->selectedPosting->items as $item) {
            $qtySisa = $item->qty - $item->returned_qty;
            if ($qtySisa > 0) {
                $this->returnItems[$item->id] = [
                    'from_tool_id' => $item->from_tool_id,
                    'to_tool_id' => $item->to_tool_id,
                    'kode_material' => $item->fromTool->kode_material,
                    'nama_tool' => $item->fromTool->deskripsi,
                    'qty_dipinjam' => $item->qty,
                    'qty_sudah_kembali' => $item->returned_qty,
                    'qty_sisa' => $qtySisa,
                    'qty_akan_dikembalikan' => 0,
                ];
            }
        }

        $this->showModal = true;
    }

    public function processReturn()
    {
        $this->validate([
            'returnItems.*.qty_akan_dikembalikan' => 'required|integer|min:0',
        ]);
        try {

            $totalItemsToReturn = 0;
            foreach ($this->returnItems as $itemId => $item) {
                $qtyAkanDikembalikan = (int) $item['qty_akan_dikembalikan'];
                $totalItemsToReturn += $qtyAkanDikembalikan;

                if ($qtyAkanDikembalikan > $item['qty_sisa']) {
                    $this->addError('returnItems.' . $itemId . '.qty_akan_dikembalikan', 'Tidak boleh melebihi sisa.');
                    throw new \Exception('Qty return melebihi qty pinjam');
                    return;
                }
            }

            if ($totalItemsToReturn <= 0) {
                $this->addError('general', 'Jumlah total pengembalian harus lebih dari 0.');
                return;
            }

            DB::transaction(function () {
                $history = MaterialTransferHistory::create([
                    'material_transfer_posting_id' => $this->selectedPosting->id,
                    'action' => 'RETURNED',
                    'notes' => 'Transfer Posting returned by ' . auth()->user()->name,
                    'user_id' => auth()->id(),
                    'returned_at' => now(),
                ]);

                foreach ($this->returnItems as $itemId => $itemData) {
                    $qtyToReturn = (int) $itemData['qty_akan_dikembalikan'];
                    if ($qtyToReturn > 0) {
                        $postingItem = $this->selectedPosting->items()->find($itemId);
                        $postingItem->increment('returned_qty', $qtyToReturn);

                        $sourceMaterialStock = ToolProjectStock::where('project_id', $this->selectedPosting->from_project_id)
                            ->where('tool_id', $itemData['from_tool_id'])
                            ->where('posisi_ct_id', $this->selectedPosting->from_posisi_ct_id)
                            ->first();

                        // qty di project asal ditambah, karena material yang dipinjam dikembalikan
                        $sourceMaterialStock->increment('qty', $qtyToReturn);
                        ///// stock peminjam dikurangi, karena material yang dipinjam dikembalikan
                        $materialPeminjam = Tools::where('kode_material', $itemData['kode_material'])->where('posisi_ct_id', $this->selectedPosting->to_posisi_ct_id)->first();
                        if (!$materialPeminjam) {
                            throw new \Exception('Material dengan kode ' . $itemData['kode_material'] . ' tidak ditemukan di posisi CT peminjam.');
                        }
                        $peminjamMaterialStock = ToolProjectStock::where('project_id', $this->selectedPosting->to_project_id)
                            ->where('tool_id', $materialPeminjam->id)
                            ->where('posisi_ct_id', $this->selectedPosting->to_posisi_ct_id)
                            ->first();
                        $peminjamMaterialStock->decrement('qty', $qtyToReturn);

                        $history->items()->create([
                            'tool_id' => $itemData['from_tool_id'],
                            'user_id' => auth()->id(),
                            'qty_returned' => $qtyToReturn,
                        ]);
                    }
                }

                // Update status transfer posting
                $this->selectedPosting->load('items');
                $totalPinjam = $this->selectedPosting->items->sum('qty');
                $totalReturned = $this->selectedPosting->items->sum('returned_qty');

                if ($totalReturned >= $totalPinjam) {
                    $this->selectedPosting->status = 'RETURNED';
                } else {
                    $this->selectedPosting->status = 'PARTIAL_RETURN';
                }
                $this->selectedPosting->save();
            });

            session()->flash('success', 'Pengembalian tool berhasil diproses.');
            $this->closeModal();
        } catch (\Throwable $e) {
            session()->flash('error', 'Pengembalian tool gagal diproses.');
            $this->closeModal();
        }
    }

    /**
     * Menutup modal dan mereset state.
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedPosting = null;
        $this->returnItems = [];
        $this->resetErrorBag();
    }

    // Menampilkan daftar peminjaman
    public function render()
    {
        $postings = MaterialTransferPosting::with(['fromProject', 'toProject', 'items.fromTool'])
            ->whereIn('status', ['ON_LOAN', 'PARTIAL_RETURN'])
            ->latest()
            ->paginate(10);

        return view('livewire.transfer-posting.return-transfer', [
            'postings' => $postings,
        ]);
    }
}
