@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="card-title mb-0">Detail Pengurangan Tool </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informasi Pengurangan</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Tanggal Pengurangan</th>
                            <td>{{ $penguranganTool->tanggal_pengurangan->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td>{{ $penguranganTool->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alasan Pengurangan</th>
                            <td>{{ $penguranganTool->alasan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Stok Sebelum</th>
                            <td>{{ $penguranganTool->qty_sebelum }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Dikurangi</th>
                            <td>{{ $penguranganTool->qty_dikurangi }}</td>
                        </tr>
                        <tr>
                            <th>Stok Sesudah</th>
                            <td>{{ $penguranganTool->qty_sesudah }}</td>
                        </tr>
                        <tr>
                            <th>Dokumen</th>
                            <td>
                                @if ($penguranganTool->document_path)
                                    <a href="{{ Storage::url($penguranganTool->document_path) }}" target="_blank"
                                        class="btn btn-sm btn-info">Lihat Dokumen</a>
                                @else
                                    Tidak Ada
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="mt-4">Informasi Distribusi Terkait Kerusakan</h5>
                    @if (isset(
                            $penguranganTool->detailKerusakanTool,
                            $penguranganTool->detailKerusakanTool->detailDaftarDistribusiTool,
                            $penguranganTool->detailKerusakanTool->detailDaftarDistribusiTool->daftarDistribusiTool,
                            $penguranganTool->detailKerusakanTool->detailDaftarDistribusiTool->daftarDistribusiTool->distribusiTool))
                        <table class="table table-bordered">
                            <tr>
                                <th>Nama Peminjam/Penerima</th>
                                <td>{{ $penguranganTool->detailKerusakanTool->detailDaftarDistribusiTool->daftarDistribusiTool->distribusiTool->nama_peminjam ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>NIP</th>
                                <td>{{ $penguranganTool->detailKerusakanTool->detailDaftarDistribusiTool->daftarDistribusiTool->distribusiTool->nip ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Project</th>
                                <td>{{ $penguranganTool->detailKerusakanTool->detailDaftarDistribusiTool->daftarDistribusiTool->distribusiTool->project->nama_project ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Departemen</th>
                                <td>{{ $penguranganTool->detailKerusakanTool->detailDaftarDistribusiTool->daftarDistribusiTool->distribusiTool->departemen->nama_departemen ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Bagian</th>
                                <td>{{ $penguranganTool->detailKerusakanTool->detailDaftarDistribusiTool->daftarDistribusiTool->distribusiTool->bagian->nama_bagian ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Unit</th>
                                <td>{{ $penguranganTool->detailKerusakanTool->detailDaftarDistribusiTool->daftarDistribusiTool->distribusiTool->unit->nama_unit ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Tipe Distribusi</th>
                                <td>{{ $penguranganTool->detailKerusakanTool->detailDaftarDistribusiTool->daftarDistribusiTool->distribusiTool->type ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Distribusi</th>
                                <td>{{ $penguranganTool->detailKerusakanTool->detailDaftarDistribusiTool->daftarDistribusiTool->distribusiTool->tanggal_pinjam_diserahkan->format('d M Y') ?? '-' }}
                                </td>
                            </tr>
                        </table>
                    @else
                        <p>Informasi distribusi terkait kerusakan tidak tersedia.</p>
                    @endif

                </div>
            </div>
            <hr>
            <h5>Informasi Tool</h5>
            @if ($penguranganTool->tool)
                <table class="table table-bordered">
                    <tr>
                        <th>Kode Material</th>
                        <td>{{ $penguranganTool->tool->kode_material }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $penguranganTool->tool->deskripsi }}</td>
                    </tr>
                    <tr>
                        <th>Spesifikasi</th>
                        <td>{{ $penguranganTool->tool->spesifikasi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Satuan</th>
                        <td>{{ $penguranganTool->tool->satuan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>{{ $penguranganTool->tool->jenis_material ?? '-' }}</td>
                    </tr>
                </table>
            @else
                <p>Informasi tool tidak tersedia.</p>
            @endif
            <h5>Detail Kerusakan Tool</h5>
            @if ($penguranganTool->detailKerusakanTool)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <tr>
                                <th>Tanggal SPR</th>
                                <td>{{ $penguranganTool->detailKerusakanTool->tanggal_spr->format('d M Y') ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Status Perbaikan</th>
                                <td>{{ $penguranganTool->detailKerusakanTool->status_perbaikan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Dokumen SPR</th>
                                <td>
                                    @if ($penguranganTool->document_path)
                                        <a href="{{ Storage::url($penguranganTool->detailKerusakanTool->file_spr) }}"
                                            target="_blank" class="btn btn-sm btn-info">Lihat Dokumen</a>
                                    @else
                                        Tidak Ada
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @else
                <p>Tidak ada detail kerusakan tool yang terkait.</p>
            @endif

            <div class="mt-4">
                <a href="{{ route('pengurangan-tool.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
@endsection
