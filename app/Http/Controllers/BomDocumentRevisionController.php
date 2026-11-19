<?php

namespace App\Http\Controllers;

use App\Models\BillOfMaterialRevision;
use App\Support\ActivityLogger;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BomDocumentRevisionController extends Controller
{
    public function show(string $id){
        $data = BillOfMaterialRevision::find($id);

        ActivityLogger::record('view', 'Lihat dokumen Bill Of Material', [
            'file' => $data->original_name_file ?? null,
            'revision_id' => $id,
        ], $data, 'BillOfMaterialRevision');

        $path = storage_path('app/public/'. $data->path_file);

        return response()->file($path, [
            'Content-Disposition' => 'inline; filename="' . $data->original_name_file . '"',
        ]);
    }

    public function delete(string $id){
        $revision = BillOfMaterialRevision::findOrFail($id);
        $bomId = $revision->bom_id;
        $path = $revision->path_file;

        try {
            DB::transaction(function () use ($revision) {
                $revision->delete();
            });
            Storage::disk('public')->delete($path);

            return redirect()->route('bill-of-material.edit', $bomId)->with('success', 'Dokumen berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus dokumen BOM: ' . $e->getMessage(), ['revision_id' => $id]);

            return redirect()->route('bill-of-material.edit', $bomId)->with('error', 'Gagal menghapus dokumen.');
        }
    }

    public function download(string $id){
        $revision = BillOfMaterialRevision::findOrFail($id);

        ActivityLogger::record('download', 'Download dokumen Bill Of Material', [
            'file' => $revision->original_name_file,
            'revision_id' => $id,
        ], $revision, 'BillOfMaterialRevision');

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->download(
            $revision->path_file,
            $revision->original_name_file
        );
    }
}
