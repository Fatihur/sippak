<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Disposisi Perorangan - {{ $pengaduan->nomor_tiket }}</title>
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
        .nomor { text-align: center; font-size: 11px; margin-bottom: 16px; color: #4b5563; }
        .isi { line-height: 1.7; text-align: justify; margin-bottom: 20px; }
        .isi p { margin: 6px 0; }
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

    <div class="judul">DISPOSISI PERORANGAN</div>
    <div class="nomor">Nomor: {{ $pengaduan->nomor_tiket }}</div>

    <div class="isi">
        <p>Kepada Yth.</p>
        <p><strong>Kepala Bidang PPA</strong></p>
        <p>Dinas P2KBP3A Kabupaten Sumbawa</p>
        <p>&nbsp;</p>
        <p>Dengan hormat,</p>
        <p>Bersama ini kami sampaikan laporan pengaduan atas nama <strong>{{ $pengaduan->nama_pelapor }}</strong> dengan nomor tiket <strong>{{ $pengaduan->nomor_tiket }}</strong> yang memerlukan tindak lanjut dari Kepala Bidang PPA.</p>
        <p>&nbsp;</p>
        <p>Demikian disposisi ini kami sampaikan untuk dapat ditindaklanjuti sebagaimana mestinya.</p>
        <p>&nbsp;</p>
        <p>Terima kasih.</p>
    </div>

    <div class="ttd-block">
        <div class="ttd-box">
            <div class="lokasi-tgl">Sumbawa, {{ now()->format('d F Y') }}</div>
            <div class="jabatan">Kepala Bidang PPA</div>
            <div class="nama">{{ $kabid->name ?? '-' }}</div>
            <div class="nip">NIP. {{ $kabid->nip ?? '-' }}</div>
        </div>
    </div>
</body>
</html>
