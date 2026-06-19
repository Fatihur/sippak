<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pengaduan {{ $pengaduan->nomor_tiket }}</title>
    <style>
        @page { margin: 12mm 10mm; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 0;
        }
        .wrapper {
            border: 2px dashed #1f2937;
            padding: 14px 16px;
            border-radius: 6px;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #1f2937;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 14px;
            margin: 0;
            letter-spacing: 1px;
        }
        .header p {
            margin: 2px 0 0;
            font-size: 9px;
            color: #4b5563;
        }
        .title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            margin: 6px 0 10px;
            letter-spacing: 0.5px;
        }
        table.kwitansi {
            width: 100%;
            border-collapse: collapse;
        }
        table.kwitansi td {
            padding: 4px 0;
            vertical-align: top;
        }
        table.kwitansi td.label {
            width: 38%;
            color: #4b5563;
            font-weight: bold;
        }
        .ticket {
            text-align: center;
            margin: 10px 0;
            padding: 8px;
            border: 1px solid #1f2937;
            border-radius: 4px;
            background: #f9fafb;
        }
        .ticket span {
            display: block;
            font-size: 9px;
            color: #4b5563;
            letter-spacing: 1px;
        }
        .ticket strong {
            display: block;
            font-size: 16px;
            margin-top: 2px;
            letter-spacing: 1px;
        }
        .footer {
            margin-top: 14px;
            border-top: 1px dashed #1f2937;
            padding-top: 8px;
            font-size: 9px;
            color: #4b5563;
            text-align: center;
            line-height: 1.5;
        }
        .status {
            display: inline-block;
            padding: 2px 8px;
            border: 1px solid #1f2937;
            border-radius: 999px;
            font-weight: bold;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>SILAPAK</h1>
            <p>Sistem Informasi Pelayanan Pengaduan</p>
            <p>Dinas P2KBP3A Kabupaten Sumbawa</p>
        </div>

        <div class="title">BUKTI PENGADUAN</div>

        <div class="ticket">
            <span>NOMOR TIKET</span>
            <strong>{{ $pengaduan->nomor_tiket }}</strong>
        </div>

        <table class="kwitansi">
            <tr>
                <td class="label">Status</td>
                <td><span class="status">{{ $pengaduan->status_label }}</span></td>
            </tr>
            <tr>
                <td class="label">Tanggal Lapor</td>
                <td>{{ $pengaduan->created_at?->format('d/m/Y H:i') }} WITA</td>
            </tr>
            <tr>
                <td class="label">Nama Pelapor</td>
                <td>{{ $pengaduan->nama_pelapor }}</td>
            </tr>
            <tr>
                <td class="label">Nomor WhatsApp</td>
                <td>{{ $pengaduan->nomor_whatsapp }}</td>
            </tr>
            <tr>
                <td class="label">Kecamatan</td>
                <td>{{ $pengaduan->kecamatan ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Kekerasan</td>
                <td>{{ $pengaduan->jenis_kekerasan }}</td>
            </tr>
            <tr>
                <td class="label">Lokasi Kejadian</td>
                <td>{{ $pengaduan->lokasi_kejadian }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Kejadian</td>
                <td>{{ $pengaduan->tanggal_kejadian?->format('d/m/Y') }}</td>
            </tr>
        </table>

        <div class="footer">
            Simpan dokumen ini sebagai bukti pelaporan resmi.<br>
            Pantau perkembangan laporan Anda melalui:<br>
            <strong>{{ url('/tracking') }}</strong><br><br>
            Dokumen ini dihasilkan otomatis oleh SILAPAK dan sah tanpa tanda tangan.
        </div>
    </div>
</body>
</html>