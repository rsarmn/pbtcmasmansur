<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $meta['title'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12px;
            color: #666;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-box {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
        .stat-box .label {
            font-size: 10px;
            color: #666;
            margin-bottom: 8px;
        }
        .stat-box .value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            font-size: 10px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-corporate {
            background: #e3f2fd;
            color: #1976d2;
        }
        .badge-individu {
            background: #fff3e0;
            color: #f57c00;
        }
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        .badge-konfirmasi {
            background: #cfe2ff;
            color: #084298;
        }
        .badge-paid, .badge-lunas {
            background: #d1e7dd;
            color: #0f5132;
        }
        .badge-rejected {
            background: #f8d7da;
            color: #842029;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .summary p {
            margin-bottom: 5px;
            font-size: 11px;
        }
        .print-instruction {
            background: #fff3cd;
            border: 1px solid #ffecb5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .print-instruction strong {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            color: #856404;
        }
        .print-instruction p {
            font-size: 12px;
            color: #664d03;
        }
        .no-print {
            text-align: center;
            margin-top: 20px;
        }
        .no-print button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 25px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
        }
        .no-print button:hover {
            background: #0056b3;
        }
        .no-print button.secondary {
            background: #6c757d;
        }
        .no-print button.secondary:hover {
            background: #545b62;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .print-instruction,
            .no-print {
                display: none !important;
            }
            .stats-grid {
                page-break-inside: avoid;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="print-instruction no-print">
            <strong>📄 Tekan CTRL+P (atau CMD+P) untuk print ke PDF</strong>
            <p>Pilih "Save as PDF" atau "Microsoft Print to PDF" sebagai printer, lalu klik Save</p>
        </div>

        <div class="header">
            <h1>{{ $meta['title'] }}</h1>
            <p>Periode: {{ $meta['month'] }} | Dicetak: {{ $meta['date'] }}</p>
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <div class="label">Total Kamar</div>
                <div class="value">{{ $meta['total_kamar'] }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Kamar Kosong</div>
                <div class="value">{{ $meta['kamar_kosong'] }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Kamar Terisi</div>
                <div class="value">{{ $meta['kamar_terisi'] }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Total Booking</div>
                <div class="value">{{ $meta['total_booking'] }}</div>
            </div>
        </div>

        @if($bookings->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Pengunjung</th>
                    <th style="width: 80px;">Jenis Tamu</th>
                    <th style="width: 90px;">Check-in</th>
                    <th style="width: 90px;">Check-out</th>
                    <th>Kamar</th>
                    <th style="width: 90px;">Status</th>
                    <th style="width: 110px;">No. Telp</th>
                    <th style="width: 50px;">Jml Kamar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $i => $b)
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td>{{ $b->nama }}</td>
                    <td>
                        <span class="badge badge-{{ $b->jenis_tamu }}">
                            {{ $b->jenis_tamu === 'corporate' ? 'Corporate' : 'Individu' }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($b->check_in)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($b->check_out)->format('d M Y') }}</td>
                    <td>{{ $b->kode_kamar ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $b->payment_status }}">
                            @if($b->payment_status === 'pending') Pending
                            @elseif($b->payment_status === 'konfirmasi_booking') Konfirmasi
                            @elseif($b->payment_status === 'paid') Paid
                            @elseif($b->payment_status === 'lunas') Lunas
                            @elseif($b->payment_status === 'rejected') Rejected
                            @else {{ ucfirst($b->payment_status) }}
                            @endif
                        </span>
                    </td>
                    <td>{{ $b->no_telp ?? '-' }}</td>
                    <td style="text-align: center;">{{ $b->jumlah_kamar }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <p><strong>Ringkasan Booking:</strong></p>
            <p>• Total Booking: {{ $bookings->count() }} transaksi</p>
            <p>• Corporate: {{ $bookings->where('jenis_tamu', 'corporate')->count() }} | Individu: {{ $bookings->where('jenis_tamu', 'individu')->count() }}</p>
            <p>• Pending: {{ $bookings->where('payment_status', 'pending')->count() }} | Konfirmasi: {{ $bookings->where('payment_status', 'konfirmasi_booking')->count() }} | Paid: {{ $bookings->where('payment_status', 'paid')->count() }} | Lunas: {{ $bookings->where('payment_status', 'lunas')->count() }} | Rejected: {{ $bookings->where('payment_status', 'rejected')->count() }}</p>
        </div>
        @else
        <p style="text-align: center; padding: 40px; color: #999;">Tidak ada data booking untuk bulan ini</p>
        @endif

        <div class="no-print">
            <button onclick="window.print()">🖨️ Print / Save as PDF</button>
            <button class="secondary" onclick="window.close()">❌ Tutup</button>
        </div>
    </div>

    <script>
        // Auto-open print dialog after page loads
        window.addEventListener('load', function() {
            // Wait 500ms for full render
            setTimeout(function() {
                window.print();
            }, 500);
        });

        // Handle after print
        window.addEventListener('afterprint', function() {
            // Optional: close window after printing
            // window.close();
        });
    </script>
</body>
</html>
