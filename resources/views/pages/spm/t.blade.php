<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $spm['no_spm'] }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000;
            padding: 5px;
        }

        .header-table td {
            vertical-align: top;
        }

        .right-info table td {
            padding: 2px 0;
        }

        .title {
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
            text-decoration: underline;
        }

        .section {
            margin-bottom: 10px;
        }

        .signature-table td {
            text-align: center;
            vertical-align: top;
            height: 120px;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td width="30%">
                <img src="{{ public_path('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}"
                    height="80">
            </td>
            <td width="30%"></td>
            <td width="40%" class="right-info">
                <table>
                    <tr>
                        <td class="bold">Tanggal</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($spm['tanggal_spm'])->toDateString() }}</td>
                    </tr>
                    <tr>
                        <td class="bold">No</td>
                        <td>:</td>
                        <td>{{ $spm['no_spm'] }}</td>
                    </tr>
                    <tr>
                        <td class="bold">Proyek</td>
                        <td>:</td>
                        <td>{{ $spm['project']['nama_project'] }}</td>
                    </tr>
                    <tr>
                        <td class="bold">Kepada</td>
                        <td>:</td>
                        <td>{{ $spm['kepada'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="title">PERMINTAAN PENGADAAN MATERIAL</div>

    <!-- BODY TEXT -->
    <div class="section">
        <table width="100%">
            <tr>
                <td class="bold">Dari : M. RENDALPROD</td>
                <td align="right" class="bold">FINISHING</td>
            </tr>
        </table>
    </div>

    <div class="section bold">Dengan Hormat,</div>

    <div class="section bold">
        Sehubungan dengan diperlukannya material untuk segera dipasang pada kereta,
        kami mohon untuk dapat mengambil material dengan list sebagai berikut :
    </div>

    <!-- TABLE -->
    <table class="table-bordered">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode Material</th>
                <th width="20%">Deskripsi</th>
                <th width="20%">Spesifikasi</th>
                <th width="10%">Quantity</th>
                <th width="10%">Satuan</th>
                <th width="20%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($spm['items'] as $tool)
                <tr>
                    <td align="center">{{ $loop->iteration }}</td>
                    <td>{{ $tool['kode_material'] }}</td>
                    <td>{{ $tool['deskripsi'] }}</td>
                    <td>{{ $tool['spesifikasi'] }}</td>
                    <td align="center">{{ $tool['requested_qty'] }}</td>
                    <td align="center">{{ $tool['satuan'] }}</td>
                    <td>{{ $tool['keterangan'] }}</td>
                </tr>
            @endforeach

            @for ($i = count($spm['items']); $i < 10; $i++)
                <tr>
                    <td align="center">{{ $i + 1 }}</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="section bold">
        Demikian kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terimakasih.
    </div>

    <!-- SIGNATURE -->
    <table class="signature-table" width="100%">
        <tr>
            <td>
                <strong>Penerima</strong><br><br><br>
                __________________________
            </td>
            <td>
                <strong>Diserahkan</strong><br>
                <strong>{{ $spm['kepada'] }}</strong><br><br>
                __________________________
            </td>
            <td>
                <strong>Pemohon</strong><br>
                <strong>M. RENDALPROD</strong><br><br>
                <strong>HADI WURIYANTO</strong>
            </td>
        </tr>
    </table>

</body>

</html>
