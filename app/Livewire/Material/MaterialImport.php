<?php

namespace App\Livewire\Material;

use App\Imports\AwalanToolsValidationImport;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class MaterialImport extends Component
{
    use WithFileUploads;

    public $file_excel;

    public $previewData = [];

    public $importErrors = [];

    public function updatedFileExcel()
    {
        $this->validate([
            'file_excel' => 'required|mimes:xlsx,xls|max:10240',
        ], [
            'file_excel.required' => 'File Excel wajib diisi.',
            'file_excel.mimes' => 'Format file harus xlsx atau xls.',
            'file_excel.max' => 'Ukuran file maksimal 10MB.',
        ]);

        try {
            $path = $this->file_excel->getRealPath();
            $validator = new AwalanToolsValidationImport;
            Excel::import($validator, $path);
            $this->importErrors = $validator->errors;
            $previewImport = new class implements ToCollection, WithHeadingRow
            {
                public $data;

                public function collection(Collection $rows)
                {
                    $this->data = $rows;
                }
            };

            Excel::import($previewImport, $path);

            if ($previewImport->data) {
                $this->previewData = $previewImport->data->toArray();
            }

            if (empty($this->importErrors)) {
                session()->flash('success_preview', 'File valid. Silahkan klik Import Material untuk memproses.');
            } else {
                session()->flash('error_preview', 'Terdapat kesalahan pada file yang diimport.');
            }
        } catch (\Exception $e) {
            $this->addError('file_excel', 'Terjadi kesalahan saat membaca file: ' . $e->getMessage());
            $this->previewData = [];
            $this->importErrors = [];
        }
    }

    public function render()
    {
        return view('livewire.material.material-import');
    }
}
