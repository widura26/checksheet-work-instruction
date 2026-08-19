<!DOCTYPE html>
<html>

<head>
    <title>Preview Barcode</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .barcode img {
            height: 40px;
            width: 100%;
            object-fit: contain;
        }

        .text-section {
            font-size: 10px;
            line-height: 1.1;
            margin-top: 5px;
            word-wrap: break-word;
        }

        .text-section p {
            margin: 0;
            font-size: 9px;
            font-weight: 500;
        }

        .text-section strong {
            display: block;
            font-size: 10px;
            margin: 3px 0 2px 0;
        }

        .text-section span {
            display: block;
            font-size: 9px;
        }
/* container row */
.row{
display:flex;
flex-wrap:wrap;
margin-left:-10px;
margin-right:-10px;
}

/* column */
[class*="col-"]{
padding:10px;
box-sizing:border-box;
}

/* responsive column */
.col-md-4{
width:33.333%;
}

.col-md-6{
width:50%;
}

.col-lg-10{
width:83.333%;
}

/* spacing */
.mb-4{
margin-bottom:1.5rem;
}

.ms-2{
margin-left:0.5rem;
}

.g-3 > *{
padding:10px;
}

/* input group */
.input-group{
display:flex;
align-items:center;
}

/* input */
.form-control{
flex:1;
padding:8px 10px;
border:1px solid #ccc;
border-radius:4px;
font-size:14px;
}

/* button */
.btn{
padding:8px 14px;
border:none;
border-radius:4px;
cursor:pointer;
text-decoration:none;
font-size:14px;
display:inline-block;
}

/* button colors */
.btn-primary{
background:#0d6efd;
color:white;
}

.btn-danger{
background:#dc3545;
color:white;
}

.btn-secondary{
background:#6c757d;
color:white;
}

/* label card */
.label-card{
border:1px solid #ddd;
padding:12px;
border-radius:6px;
text-align:center;
background:#fff;
}

/* text section */
.text-section p{
margin:5px 0;
font-size:14px;
}

.text-section strong{
display:block;
font-size:15px;
}

.text-section span{
font-size:13px;
color:#555;
}
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body class="container mt-4">

    <!-- Search -->
    <div class="row mb-4 no-print">
        <div class="col-md-6 col-lg-10">
            <form method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control"
                        placeholder="Masukkan kode material (pisahkan dengan koma tanpa spasi jika lebih dari 1)"
                        value="{{ request('search') }}">
                    <button class="btn btn-primary">Cari</button>
                    <a href="{{ route('barcode.print') }}" class="btn btn-danger ms-2">Reset</a>
                    <a href="{{ route('daftar-kode-material.index') }}" class="btn btn-secondary ms-2">Kembali</a>
                </div>
            </form>
        </div>

        {{-- <div class="col-md-6 text-end">
            <button onclick="window.print()" class="btn btn-success">
                Print
            </button>
        </div> --}}
    </div>

    <!-- Barcode Grid -->
    <div class="row g-3">
        @foreach ($items as $item)
            <div class="col-md-4">
                <div class="label-card">
                    <div class="barcode">
                        <img src="data:image/png;base64,{{ $item->barcode_base64 }}">
                    </div>

                    <div class="text-section">
                        <p>{{ $item->kode_material }}</p>
                        <strong>{{ $item->deskripsi }}</strong>
                        <span>{{ $item->spesifikasi }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</body>

</html>
