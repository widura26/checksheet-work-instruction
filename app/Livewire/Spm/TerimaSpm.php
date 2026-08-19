<?php

namespace App\Livewire\Spm;

use App\Http\Service\SpmReceiveService;
use App\Models\Spm;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class TerimaSpm extends Component
{
    use WithFileUploads;
    public Spm $spm;

    public array $terimaItems = [];
    public $file_spm;
    public function mount(Spm $spm)
    {
        $this->spm = $spm->load(['items', 'project' => function ($q) {
            $q->withoutGlobalScope('onlyWithStock');
        }]);
    }

    protected function rules()
    {
        $rules = [
            'terimaItems' => 'required|array',
            'file_spm'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];

        foreach ($this->spm->items as $item) {
            $sisa = $item->requested_qty - $item->qty_diterima;
            $rules['terimaItems.' . $item->id . '.qty_diterima'] = 'required|integer|min:0|max:' . $sisa;
        }

        return $rules;
    }

    protected function messages()
    {
        $messages = [];
        foreach ($this->spm->items as $item) {
            $messages['terimaItems.' . $item->id . '.qty_diterima.required'] = 'Qty diterima harus diisi.';
            $messages['terimaItems.' . $item->id . '.qty_diterima.integer'] = 'Qty harus angka.';
            $messages['terimaItems.' . $item->id . '.qty_diterima.min'] = 'Qty tidak boleh kurang dari 0.';
            $messages['terimaItems.' . $item->id . '.qty_diterima.max'] = 'Qty diterima tidak boleh melebihi sisa permintaan.';
        }

        return $messages;
    }

    public function save(SpmReceiveService $service)
    {
        $this->validate();

        if (collect($this->terimaItems)->pluck('qty_diterima')->sum() <= 0) {
            $this->addError('terimaItems', 'Tidak ada barang yang diterima.');

            return;
        }

        try {
            $path = $this->uploadFileSpm();
            $this->spm->update([
                'file_ttd' => $path,
            ]);
            $service->receive($this->spm, $this->terimaItems);
            session()->flash('success', 'Barang berhasil diterima.');
            return redirect()->route('spm.index');
        } catch (\Exception $e) {
            Log::error('Error menerima SPM: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ');
        }
    }
    private function uploadFileSpm()
    {
        try {
            if ($this->spm->file_ttd && Storage::disk('public')->exists($this->spm->file_ttd)) {
                Storage::disk('public')->delete($this->spm->file_ttd);
            }
            $fileName = str_replace('/', '-', $this->spm->no_spm)
                . '.' . $this->file_spm->getClientOriginalExtension();
            $path = $this->file_spm->storeAs(
                'spm_files_ttd',
                $fileName,
                'public'
            );
            return $path;
        } catch (\Throwable $e) {
            throw new \Exception('Gagal mengunggah file SPM: ' . $e->getMessage());
        }
    }
    public function render()
    {
        $this->spm->loadMissing([
            'project' => fn($q) => $q->withoutGlobalScope('onlyWithStock'),
        ]);
        return view('livewire.spm.terima-spm');
    }
}
