<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Kinerja - {{ $fakultasKomandan }} - {{ $namaBulan[(int)$bulan] }} {{ $tahun }}</title>
    <link href="[https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css](https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css)" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
        }
        .header-container {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
        }
        .header-container img {
            width: 80px;
            height: auto;
            position: absolute;
            left: 20px;
            top: 20px;
        }
        .report-title {
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
         .report-subtitle {
            font-size: 1rem;
            margin-bottom: 15px;
        }
        .report-period {
            font-size: 1.1rem;
            font-weight: bold;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .table th, .table td {
            border: 1px solid #000 !important;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }
        .table th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 300px;
            float: right;
            text-align: center;
        }
        .signature-box .signature-name {
            margin-top: 70px;
            font-weight: bold;
            text-decoration: underline;
        }
        .signature-box .signature-nik {
            margin-top: 5px;
        }

        @media print {
            body {
                margin: 0;
                color: #000;
            }
            .container {
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="header-container">
            {{-- Ganti URL logo ini dengan URL logo instansi Anda --}}
            <img src="{{ asset('assets/img/Undip Logo.png') }}" alt="Logo">
            <h4>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,<br>RISET, DAN TEKNOLOGI</h4>
            <h4 class="fw-bold">UNIVERSITAS DIPONEGORO</h4>
            <small>Jalan Prof. H. Soedarto, S.H., Tembalang, Semarang, Jawa Tengah 50275</small>
        </div>

        <h3 class="text-center fw-bold text-decoration-underline mb-3">LAPORAN KINERJA BULANAN</h3>
        
        <table class="table table-borderless table-sm mb-4" style="font-size: 14px;">
            <tr>
                <td style="width: 15%;">Unit Kerja</td>
                <td style="width: 2%;">:</td>
                <td>Satuan Pengamanan {{ $fakultasKomandan }}</td>
            </tr>
             <tr>
                <td>Periode</td>
                <td>:</td>
                <td>{{ $namaBulan[(int)$bulan] }} {{ $tahun }}</td>
            </tr>
        </table>
       

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama Petugas</th>
                    <th>Hari Kerja Terjadwal</th>
                    <th>Hari Hadir Aktual</th>
                    <th>Persentase Kehadiran (%)</th>
                    <th>Total Jam Kerja Terjadwal</th>
                    <th>Total Jam Kerja Aktual</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporanKinerjaData as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-start">{{ $data['nik'] }}</td>
                    <td class="text-start">{{ $data['nama_lengkap'] }}</td>
                    <td>{{ $data['jumlah_hari_kerja_terjadwal'] }}</td>
                    <td>{{ $data['jumlah_hari_hadir'] }}</td>
                    <td>{{ $data['persentase_kehadiran'] }}%</td>
                    <td>{{ $data['total_jam_kerja_jadwal_format'] }}</td>
                    <td>{{ $data['total_jam_kerja_aktual_format'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data kinerja untuk ditampilkan pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="signature-section">
            <div class="signature-box">
                <div>Semarang, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                <div>Komandan Satuan Pengamanan</div>
                <div> {{ $fakultasKomandan }}</div>
                <div class="signature-name">{{ $komandan->nama_lengkap }}</div>
                <div class="signature-nik">NIK. {{ $komandan->nik }}</div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>