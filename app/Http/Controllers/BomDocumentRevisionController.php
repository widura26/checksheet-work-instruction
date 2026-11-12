<?php

namespace App\Http\Controllers;

use App\Models\BillOfMaterialRevision;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class BomDocumentRevisionController extends Controller
{
    public function show(string $id){
        $data = BillOfMaterialRevision::find($id);
        $path = storage_path('app/public/'. $data->path_file);

        return response()->file($path, [
            'Content-Disposition' => 'inline; filename="' . $data->original_name_file . '"',
        ]);
    }

    public function delete(string $id){
        $revision = BillOfMaterialRevision::findOrFail($id);
        $bomId = $revision->bom_id;

        Storage::disk('public')->delete($revision->path_file);
        $revision->delete();

        return redirect()->route('bill-of-material.edit', $bomId)->with('success', 'Dokumen berhasil dihapus.');
    }

    public function download(string $id){
        $revision = BillOfMaterialRevision::findOrFail($id);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->download(
            $revision->path_file,
            $revision->original_name_file
        );
    }
}
