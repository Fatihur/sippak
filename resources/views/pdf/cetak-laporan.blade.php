<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengaduan {{ $pengaduan->nomor_tiket }}</title>
    <style>
        @page { margin: 15mm 12mm; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 0;
        }
        .kop {
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 4px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .logo { width: 65px; height: 65px; object-fit: contain; }
        .kop-text { flex: 1; text-align: center; }
        .kop-text h1 { margin: 0; font-size: 14px; color: #0f172a; line-height: 1.4; }
        .kop-text p { margin: 2px 0 0; font-size: 10px; color: #4b5563; }
        .judul { text-align: center; margin: 14px 0; font-size: 13px; font-weight: bold; text-decoration: underline; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data td { padding: 5px 8px; vertical-align: top; border: 1px solid #cbd5e1; font-size: 11px; }
        table.data td.label { width: 35%; background: #f1f5f9; font-weight: bold; color: #334155; }
        .section-title { font-weight: bold; margin: 14px 0 6px; font-size: 11px; color: #0f172a; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; }
        .kronologi { margin-top: 6px; line-height: 1.6; text-align: justify; }
        .ttd-block {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }
        .ttd-box {
            width: 220px;
            text-align: center;
        }
        .ttd-box .lokasi-tgl {
            margin-bottom: 50px;
            font-size: 11px;
        }
        .ttd-box .jabatan {
            font-weight: bold;
            text-decoration: underline;
            font-size: 11px;
        }
        .ttd-box .nama {
            font-weight: bold;
            font-size: 11px;
            margin-top: 2px;
        }
        .ttd-box .nip {
            font-size: 10px;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="kop">
        <img class="logo" src="{{ public_path('logo-sumbawa.png') }}" alt="Logo">
        <div class="kop-text">
            <h1>PEMERINTAH KABUPATEN SUMBAWA<br>DINAS PENGENDALIAN PENDUDUK, KELUARGA BERENCANA,<br>PEMBERDAYAAN PEREMPUAN DAN PERLINDUNGAN ANAK</h1>
            <p>Jl. Garuda No. 1 Sumbawa Besar</p>
        </div>
    </div>

    <div class="judul">LAPORAN PENGADUAN</div>

    <table class="data">
        <tr>
            <td class="label">Nomor Tiket</td>
            <td>{{ $pengaduan->nomor_tiket }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Lapor</td>
            <td>{{ $pengaduan->created_at?->format('d/m/Y H:i') }} WITA</td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td>{{ $pengaduan->status_label }}</td>
        </tr>
        <tr>
            <td class="label">Pelapor</td>
            <td>{{ $pengaduan->nama_pelapor }}</td>
        </tr>
        <tr>
            <td class="label">NIK Pelapor</td>
            <td>{{ $pengaduan->nik_pelapor }}</td>
        </tr>
        <tr>
            <td class="label">Nomor WhatsApp</td>
            <td>{{ $pengaduan->nomor_whatsapp }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td>{{ $pengaduan->email_pelapor ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td>{{ $pengaduan->alamat_pelapor }}</td>
        </tr>
        <tr>
            <td class="label">Kecamatan</td>
            <td>{{ $pengaduan->kecamatan ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Korban</td>
            <td>{{ $pengaduan->nama_korban }}</td>
        </tr>
        <tr>
            <td class="label">Umur / Jenis Kelamin</td>
            <td>{{ $pengaduan->umur_korban }} tahun / {{ $pengaduan->jenis_kelamin_korban }}</td>
        </tr>
        <tr>
            <td class="label">Hubungan dengan Pelapor</td>
            <td>{{ $pengaduan->hubungan_dengan_pelapor }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Kekerasan</td>
            <td>{{ $pengaduan->jenis_kekerasan }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Kejadian</td>
            <td>{{ $pengaduan->tanggal_kejadian?->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Lokasi Kejadian</td>
            <td>{{ $pengaduan->lokasi_kejadian }}</td>
        </tr>
    </table>

    <div class="section-title">KRONOLOGI KEJADIAN</div>
    <div class="kronologi">{{ $pengaduan->kronologi_kejadian }}</div>

    <div class="ttd-block">
        <div class="ttd-box">
            <div class="lokasi-tgl">Sumbawa, {{ $pengaduan->created_at?->format('d F Y') }}</div>
            <div class="jabatan">Kepala Bidang PPA</div>
            <div class="nama">{{ $kabid->name ?? '-' }}</div>
            <div class="nip">NIP. {{ $kabid->nip ?? '-' }}</div>
        </div>
    </div>
</body>
</html>
