<?php

namespace App\Http\Service;

use App\Models\DaftarDistribusiTool;
use App\Models\DistribusiTool;
use App\Models\Spm;
use App\Models\SpmItem;
use App\Models\SpmItemsHistory;
use App\Models\ToolProjectStock;
use App\Models\Tools;
use App\Models\UpdateStockTool;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SpmReceiveService
{
    private ?DistribusiTool $workshopDistribusi = null;

    public function receive(Spm $spm, array $receivedItems)
    {
        try {
            DB::transaction(function () use ($spm, $receivedItems) {
                foreach ($receivedItems as $itemId => $itemData) {
                    $qtyReceived = (int) $itemData['qty_diterima'];
                    if ($qtyReceived <= 0) {
                        continue;
                    }
                    $spmItem = SpmItem::findOrFail($itemId);
                    $this->handleItemReceival($spm, $spmItem, $qtyReceived);
                }
                $this->updateSpmStatus($spm);
            });
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    private function handleItemReceival(Spm $spm, SpmItem $spmItem, int $qtyReceived)
    {
        try {
            // isinya Tools atau [sudahAda,baru]
            [$tool, $statusTool] = $this->findOrCreateTool($spmItem, $spm);
            $this->updateProjectStock($tool, $spm->project_id, $qtyReceived, $spm->no_spm, $statusTool, $spm->kepada);

            SpmItemsHistory::create([
                'spm_item_id' => $spmItem->id,
                'tool_id' => $tool->id,
                'posisi_ct_id' => $tool->posisi_ct_id,
                'received_by' => Auth::id(),
                'qty' => $qtyReceived,
                'received_at' => now(),
            ]);
            if ($spm->destinasi === 'workshop') {
                $distribusi = $this->createWorkshopDistribution($spm);
                $this->createWorkshopDistributionItem(
                    $distribusi,
                    $tool,
                    $qtyReceived,
                    $spm
                );
            }
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    private function findOrCreateTool(SpmItem $spmItem, Spm $spm): array
    {
        try {
            if ($spmItem->tool_id) {
                $toolsAda = Tools::where('kode_material', $spmItem->kode_material)
                    ->where('id', $spmItem->tool_id)
                    ->whereHas('posisiCT', fn($q) => $q->where('id', $spm->posisi_ct_id))
                    ->first();

                return [$toolsAda, 'sudahAda'];
            }
            $tool = Tools::create([
                'kode_material' => $spmItem->kode_material,
                'deskripsi' => $spmItem->deskripsi,
                'spesifikasi' => $spmItem->spesifikasi,
                'satuan' => $spmItem->satuan,
                'vendor' => 'vendor',
                'keterangan_vendor' => $spm->kepada,
                'project_id' => $spm->project_id,
                'jenis_material' => $spmItem->kategori,
                'posisi_ct_id' => $spm->posisi_ct_id,
                'tanggal_ditambahkan' => now(),
            ]);

            $spmItem->tool_id = $tool->id;
            $spmItem->save();

            return [$tool, 'baru'];
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    private function updateProjectStock(Tools $tool, string $projectId, int $quantity, string $spmNumber, string $statusTool, string $kepada)
    {
        try {
            $stock = ToolProjectStock::firstOrCreate(
                [
                    'tool_id' => $tool->id,
                    'project_id' => $projectId,
                    'posisi_ct_id' => $tool->posisi_ct_id,
                ],
                [
                    'qty' => 0,
                ]
            );
            $qtySebelum = $stock->qty;
            $stock->increment('qty', $quantity);
            $qtySesudah = $qtySebelum + $quantity;
            UpdateStockTool::create([
                'tool_id' => $tool->id,
                'project_id' => $projectId,
                'user_id' => Auth::id(),
                'qty_sebelum' => $qtySebelum,
                'qty_update' => $quantity,
                'qty_sesudah' => $qtySesudah,
                'type' => $statusTool == 'sudahAda' ? 'tambah_stok' : 'input_material',
                'tanggal_update' => now(),
                'dokumen_pengambilan' => 'spm',
                'nomor_dokumen' => $spmNumber,
                'vendor' => 'vendor',
                'keterangan_vendor' => $kepada,
                'dokumen_pengambilan_path' => null,
            ]);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    private function createWorkshopDistribution(Spm $spm): DistribusiTool
    {
        if ($this->workshopDistribusi !== null) {
            return $this->workshopDistribusi;
        }

        $this->workshopDistribusi = DistribusiTool::create([
            'nama_peminjam' => 'SPM',
            'user_id' => $spm->created_by,
            'departemen_id' => $spm->departemen->id,
            'bagian_id' => $spm->bagian->id,
            'unit_id' => $spm->unit->id,
            'nip' => 'N/A',
            'project_id' => $spm->project_id,
            'type' => 'penyerahan',
            'tanggal_pinjam_diserahkan' => now(),
            'status' => 'diserahkan',
            'keterangan' => 'Penyerahan langsung dari SPM ' . $spm->no_spm,
        ]);

        return $this->workshopDistribusi;
    }

    private function createWorkshopDistributionItem(DistribusiTool $distribusi, Tools $tool, int $quantity, Spm $spm)
    {
        $stock = ToolProjectStock::where([
            'tool_id' => $tool->id,
            'project_id' => $spm->project_id,
            'posisi_ct_id' => $spm->posisi_ct_id,
        ])->firstOrFail();

        if ($stock->qty < $quantity) {
            throw new \Exception(
                'Stok untuk tool ' . $tool->kode_material . ' tidak mencukupi.'
            );
        }

        DaftarDistribusiTool::create([
            'distribusi_tools_id' => $distribusi->id,
            'tool_id' => $tool->id,
            'posisi_ct_id' => $spm->posisi_ct_id,
            'jumlah_distribusi' => $quantity,
        ]);

        $stock->decrement('qty', $quantity);
    }

    private function updateSpmStatus(Spm $spm)
    {
        try {
            $spm->refresh();
            $spm->load(['items.itemHistory']);
            $totalRequested = $spm->items()->sum('requested_qty');
            $totalReceived = $spm->items
                ->flatMap(fn($item) => $item->itemHistory)
                ->sum('qty');
            if ($totalReceived >= $totalRequested) {
                $status = 'COMPLETED';
            } elseif ($totalReceived > 0) {
                $status = 'COMPLETED';
            } else {
                $status = 'WAITING';
            }

            $spm->status = $status;
            $spm->save();
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
