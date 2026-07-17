<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Disposisi - {{ $disposisi->nomor_disposisi }}</title>
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
        .meta { text-align: center; margin-bottom: 16px; }
        .meta table { margin: 0 auto; font-size: 11px; }
        .meta td { padding: 2px 8px; text-align: left; }
        .meta td:first-child { font-weight: bold; text-align: right; }
        .prioritas-box {
            display: inline-block;
            padding: 2px 10px;
            border: 1px solid #111827;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            margin: 6px 0;
        }
        .isi { line-height: 1.7; text-align: justify; margin-bottom: 20px; }
        .isi p { margin: 6px 0; }
        .instruksi-box {
            border: 1px solid #d1d5db;
            padding: 12px 16px;
            margin: 12px 0;
            background: #f9fafb;
        }
        .instruksi-box .label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
        }
        .ttd-block {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }
        .ttd-box {
            width: 250px;
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

    <div class="judul">LEMBAR DISPOSISI</div>

    <div class="meta">
        <table>
            <tr><td>Nomor Disposisi</td><td>: {{ $disposisi->nomor_disposisi }}</td></tr>
            <tr><td>Nomor Pengaduan</td><td>: {{ $disposisi->pengaduan->nomor_tiket }}</td></tr>
            <tr><td>Tanggal Disposisi</td><td>: {{ $disposisi->tanggal_disposisi->format('d F Y') }}</td></tr>
            <tr><td>Nama Pelapor</td><td>: {{ $disposisi->pengaduan->nama_pelapor }}</td></tr>
            <tr><td>Jenis Kasus</td><td>: {{ $disposisi->pengaduan->jenis_kekerasan }}</td></tr>
            <tr><td>Dari</td><td>: {{ $disposisi->dariUser->name }} ({{ $disposisi->tingkat === 'kadis' ? 'Kepala Dinas' : 'Kepala Bidang' }})</td></tr>
            <tr><td>Tujuan</td><td>: {{ $disposisi->untukUser?->name ?: ($disposisi->tingkat === 'kadis' ? 'Kepala Bidang PPA' : 'Admin/Operator') }}</td></tr>
        </table>
    </div>

    <div style="text-align:center;">
        <span class="prioritas-box">Prioritas: {{ $disposisi->labelPrioritas() }}</span>
    </div>

    <div class="isi">
        <p>Dengan hormat,</p>
        <p>Bersama ini kami sampaikan laporan pengaduan atas nama <strong>{{ $disposisi->pengaduan->nama_pelapor }}</strong> ({{ $disposisi->pengaduan->nama_korban }}) dengan nomor tiket <strong>{{ $disposisi->pengaduan->nomor_tiket }}</strong> yang memerlukan tindak lanjut.</p>
    </div>

    @if($disposisi->instruksi)
        <div class="instruksi-box">
            <div class="label">Instruksi</div>
            <p style="margin:8px 0 0;">{{ $disposisi->instruksi }}</p>
        </div>
    @endif

    @if($disposisi->arahan_pelaksanaan)
        <div class="instruksi-box">
            <div class="label">Arahan Pelaksanaan</div>
            <p style="margin:8px 0 0;">{{ $disposisi->arahan_pelaksanaan }}</p>
        </div>
    @endif

    @if($disposisi->nama_petugas)
        <p>Petugas yang ditunjuk: <strong>{{ $disposisi->nama_petugas }}</strong></p>
    @endif

    <p style="margin-top:16px;">Demikian disposisi ini disampaikan untuk dapat ditindaklanjuti sebagaimana mestinya.</p>

    <div class="ttd-block">
        <div class="ttd-box">
            <div class="lokasi-tgl">Sumbawa, {{ $disposisi->tanggal_disposisi->format('d F Y') }}</div>
            <div class="jabatan">{{ $disposisi->tingkat === 'kadis' ? 'KEPALA DINAS' : 'KEPALA BIDANG' }} P2KBP3A</div>
            <div class="nama">{{ $disposisi->dariUser->name }}</div>
            <div class="jabatan" style="font-weight:normal;text-decoration:none;font-size:10px;">{{ $disposisi->dariUser->jabatan ?: '' }}</div>
            <div class="nip">NIP. {{ $disposisi->dariUser->nip ?: '-' }}</div>
        </div>
    </div>
</body>
</html>
