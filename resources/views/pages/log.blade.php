@extends('layouts.app')

@push('css')
    <style>
        /* Tambahkan kelas CSS untuk judul tabel agar tetap pada posisi atas saat digulir */
        .sticky-header {
            position: sticky;
            top: 0;
            background-color: #444;
            /* Warna latar belakang judul tabel */
            z-index: 1;
            /* Pastikan judul tabel tetap di atas konten tabel */
        }

        /* Atur lebar kolom agar sesuai dengan konten di dalamnya */
        #myTable th {
            width: auto !important;
        }
    </style>
@endpush
@section('content')
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Log Aktivitas</h5>
                    <input type="text" id="myInput" class="form-control" placeholder="Cari..." onkeyup="myFunction()"
                        title="Ketikkan sesuatu untuk mencari">
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height:700px; overflow-y:auto">
                        <table id="myTable" class="table table-striped">
                          <!-- Tambahkan id myTable -->
                          <thead class="bg-secondary text-white text-center sticky-header">
                            <tr>
                                <th>No</th>
                                    <th>Nama Log</th>
                                    <th>ID Subject</th>
                                    <th>Deskripsi</th>
                                    <th>User</th>
                                    <th style="max-width:200px">Properties</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($logs as $log)
                                <tr>
                                    <td>{{$log->id}}</td>
                                    <td>{{$log->log_name}} ({{$log->event}})</td>
                                    <td>{{$log->subject_id}}</td>
                                    <td>{{$log->description}}</td>
                                    <td>
                                        {{$log->causer_name}} ({{$log->causer_id}})
                                    </td>
                                    <td style="max-width:200px">{{$log->properties}}</td>
                                    <td>{{$log->created_at}}</td>
                                    <td>{{$log->updated_at}}</td>
                                </tr>
                                @endforeach
                          </tbody>
                        </table>
                      </div>
                </div>
            </div>
        </div>
    </div>

    @endsection

@push('js')

    <script type="module">
        function myFunction() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                if (tr[i].getElementsByTagName("th").length > 0) {
                    continue; // Lewati baris yang berisi header
                }
                var found = false;
                td = tr[i].getElementsByTagName("td");
                for (var j = 0; j < td.length; j++) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break; // Hentikan loop jika ditemukan kecocokan
                    }
                }
                tr[i].style.display = found ? "" : "none";
            }
        }
    </script>
@endpush

