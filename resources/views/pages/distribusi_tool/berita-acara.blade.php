<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Berita Acara</title>

    <style>
        @page {
            size: 9.5in 11in;
            margin: 5mm 15mm 15mm 15mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 4px;
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .border td,
        .border th {
            border: 1px solid #000;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mb-15 {
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <!-- HEADER LOGO -->
    <table class="mb-10">
        <tr>
            <td width="70%"></td>
            <td width="30%" class="text-center">
                <img src="{{ public_path('template/assets/img/Logo_INKA_-_Industri_Kereta_Api_Indonesia.svg') }}"
                    width="130"><br>
                <span style="font-size:13px;">DIVISI PABRIK BANYUWANGI</span>
            </td>
        </tr>
    </table>

    <!-- JUDUL -->
    <p class="text-center bold mb-10" style="font-size:13px;">
        BERITA ACARA SERAH TERIMA BARANG
    </p>

    <p class="text-center bold mb-15">
        Nomor : {{ $distribusiTool['no_bast'] }}
    </p>

    <!-- PIHAK PERTAMA -->
    <p class="bold">Kami yang bertanda tangan di bawah ini:</p>

    <table class="mb-10">
        <tr>
            <td width="25%" class="bold">Nama</td>
            <td width="3%">:</td>
            <td>{{ optional($kodeBagian)->nama_manager }}</td>
        </tr>
        <tr>
            <td class="bold">Jabatan</td>
            <td>:</td>
            <td>{{ optional($kodeBagian)->jabatan }}</td>
        </tr>
        <tr>
            <td class="bold">Bagian</td>
            <td>:</td>
            <td>{{ optional($kodeBagian)->nama_bagian }}</td>
        </tr>
        <tr>
            <td class="bold">Unit</td>
            <td>:</td>
            <td>{{ optional($kodeBagian)->nama_unit }}</td>
        </tr>
    </table>

    <p class="bold mb-10">Selanjutnya disebut Pihak Pertama</p>

    <!-- PIHAK KEDUA -->
    <table class="mb-10">
        <tr>
            <td width="25%" class="bold">Nama</td>
            <td width="3%">:</td>
            <td>{{ optional($distribusiTool)->nama_peminjam }}</td>
        </tr>
        <tr>
            <td class="bold">Jabatan</td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td class="bold">Bagian</td>
            <td>:</td>
            <td>{{ optional($distribusiTool)->bagian->nama_bagian }}</td>
        </tr>
        <tr>
            <td class="bold">Unit</td>
            <td>:</td>
            <td>{{ optional($distribusiTool)->unit->nama_unit }}</td>
        </tr>
    </table>

    <p class="bold mb-10">Selanjutnya disebut Pihak Kedua</p>

    <!-- PARAGRAF -->
    <p class="mb-10">
        PIHAK PERTAMA menyerahkan barang berupa peralatan / tools untuk kebutuhan Proyek
        <strong>{{ optional($distribusiTool)['project']['nama_project'] }}</strong> kepada PIHAK KEDUA dan PIHAK KEDUA
        menyatakan
        telah
        menerima barang dari
        PIHAK PERTAMA berupa:
    </p>

    <!-- TABEL BARANG -->
    <table class="border mb-15">
        <thead class="text-center">
            <tr>
                <th width="5%">NO</th>
                <th width="18%">KODE MATERIAL</th>
                <th width="22%">NAMA</th>
                <th width="25%">SPESIFIKASI</th>
                <th width="10%">JUMLAH</th>
                <th width="10%">SATUAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($distribusiTool['daftarDistribusiTool'] as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item['tool']['kode_material'] }}</td>
                    <td>{{ $item['tool']['deskripsi'] }}</td>
                    <td>{{ $item['tool']['spesifikasi'] }}</td>
                    <td class="text-center">{{ $item['jumlah_distribusi_formatted'] }}</td>
                    <td class="text-center">{{ $item['tool']['satuan'] }}</td>
                </tr>
            @endforeach
            @for ($i = count($distribusiTool['daftarDistribusiTool']); $i < 5; $i++)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <table>
        <tr class="text-center">
            <td width="50%">
                <strong>Yang Menyerahkan</strong><br>
                <strong>Pihak Pertama</strong><br><br><br><br><br>
                <b>{{ optional($kodeBagian)->nama_manager }}</b>
            </td>
            <td width="50%">
                Banyuwangi,
                {{ \Carbon\Carbon::parse($distribusiTool['tanggal_pinjam_diserahkan'])->locale('id')->translatedFormat('d F Y') }}
                <br>
                <strong>Yang Menerima</strong><br>
                <strong>Pihak Kedua</strong><br><br><br><br>
                <b> {{ optional($distribusiTool)->nama_peminjam }}</b>
            </td>
        </tr>
    </table>

</body>

</html>
