<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $meta['title'] ?? 'Laporan Bulanan' }}</title>

  <style>
    @page { size: A4 landscape; margin: 20px; }

    body {
      font-family: Arial, sans-serif;
      font-size: 11px;
      color: #222;
    }

    /* ================= HEADER ================= */
    .header {
      text-align: center;
      margin-bottom: 20px;
      border-bottom: 2px solid #7b1a2e;
      padding-bottom: 10px;
    }
    .header h1 {
      margin: 0;
      font-size: 18px;
      color: #7b1a2e;
    }
    .header p {
      margin: 4px 0;
      font-size: 10px;
      color: #555;
    }

    /* ================= STAT BOX ================= */
    .stats-grid {
      display: table;
      width: 100%;
      margin-top: 15px;
    }
    .stat-box {
      display: table-cell;
      width: 25%;
      padding: 8px;
      text-align: center;
      border: 1px solid #ddd;
      background: #fafafa;
    }
    .stat-value {
      font-size: 18px;
      font-weight: bold;
      color: #7b1a2e;
    }
    .stat-label {
      font-size: 9px;
      color: #666;
    }

    /* ================= TABLE ================= */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      table-layout: fixed; /* BIKIN TABEL TIDAK GESER */
    }

    th {
      background: #7b1a2e;
      color: white;
      padding: 6px;
      font-size: 10px;
      text-align: left;
    }

    td {
      border: 1px solid #ddd;
      padding: 5px;
      font-size: 10px;
      word-wrap: break-word;
    }

    tr:nth-child(even) { background: #f9f9f9; }

    /* ================= BADGE ================= */
    .badge {
      padding: 2px 5px;
      border-radius: 3px;
      font-size: 8px;
      font-weight: bold;
      display: inline-block;
    }
    .badge-corporate { background: #dbeafe; color: #1e40af; }
    .badge-individu { background: #fef3c7; color: #92400e; }
    .badge-lunas { background: #d1fae5; color: #065f46; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }

    /* ================= IMAGE FIX ================= */
    .img-bukti {
      width: 70px;
      height: 55px;
      object-fit: contain;
      display: block;
      margin: 0 auto;
      border: 1px solid #ddd;
      padding: 2px;
      background: #fff;
    }

    /* ================= FOOTER ================= */
    .footer {
      text-align: center;
      margin-top: 25px;
      font-size: 9px;
      color: #777;
    }
  </style>
</head>

<body>

  <!-- ========== HEADER ========== -->
  <div class="header">
    <h1>{{ $meta['title'] ?? 'Laporan Bulanan' }}</h1>
    <p>Periode: {{ $meta['month'] ?? '-' }}</p>
    <p>Dicetak pada: {{ $meta['date'] }}</p>
  </div>

  <!-- ========== SUMMARY BOX ========== -->
  <div class="stats-grid">
    <div class="stat-box">
      <div class="stat-value">{{ $meta['total_kamar'] }}</div>
      <div class="stat-label">Total Kamar</div>
    </div>
    <div class="stat-box">
      <div class="stat-value">{{ $meta['kamar_kosong'] }}</div>
      <div class="stat-label">Kamar Kosong</div>
    </div>
    <div class="stat-box">
      <div class="stat-value">{{ $meta['kamar_terisi'] }}</div>
      <div class="stat-label">Kamar Terisi</div>
    </div>
    <div class="stat-box">
      <div class="stat-value">{{ $meta['total_booking'] }}</div>
      <div class="stat-label">Total Booking</div>
    </div>
  </div>

  <h3 style="margin-top: 20px; color:#7b1a2e; font-size:12px;">Daftar Booking</h3>

  <!-- ========== TABLE ========== -->
  <table>
    <thead>
      <tr>
        <th style="width:3%">No</th>
        <th style="width:10%">Nama</th>
        <th style="width:6%">Jenis</th>
        <th style="width:7%">Check-in</th>
        <th style="width:7%">Check-out</th>
        <th style="width:7%">Kode</th>
        <th style="width:10%">Kamar</th>
        <th style="width:5%">Jml Kmr</th>
        <th style="width:5%">Orang</th>
        <th style="width:9%">Harga</th>
        <th style="width:7%">Status</th>
        <th style="width:6%">Identitas</th>
        <th style="width:6%">Bukti</th>
        <th style="width:10%">Asal</th>
        <th style="width:8%">No Telp</th>
      </tr>
    </thead>

    <tbody>
      @forelse($bookings as $i => $b)
      <tr>
        <td style="text-align:center">{{ $i+1 }}</td>

        <td>
          <strong>
          @if(strtolower($b->jenis_tamu) == 'corporate' && $b->nama_pic)
            {{ $b->nama_pic }}
            @if($b->nama)
              <br><small style="color:#666">{{ $b->nama }}</small>
            @endif
          @else
            {{ $b->nama }}
          @endif
          </strong>
        </td>

        <td>
          <span class="badge {{ strtolower($b->jenis_tamu) == 'corporate' ? 'badge-corporate':'badge-individu' }}">
            {{ ucfirst($b->jenis_tamu) }}
          </span>
        </td>

        <td>{{ \Carbon\Carbon::parse($b->check_in)->format('d M Y') }}</td>
        <td>{{ \Carbon\Carbon::parse($b->check_out)->format('d M Y') }}</td>
        <td>{{ $b->kode_kamar ?? '-' }}</td>

        <td>
          @php
            $ids = explode(',', $b->kode_kamar ?? '');
            $jenis = [];
            foreach ($ids as $id) {
              $km = \App\Models\Kamar::where('kode_kamar', trim($id))->first();
              if ($km && !in_array($km->jenis_kamar, $jenis)) $jenis[] = $km->jenis_kamar;
            }
          @endphp
          {{ implode(', ', $jenis) ?: '-' }}
        </td>

        <td style="text-align:center">{{ $b->jumlah_kamar ?? '-' }}</td>
        <td style="text-align:center">{{ $b->jumlah_peserta ?? '-' }}</td>

        <td>
          @if($b->total_harga)
            Rp {{ number_format($b->total_harga,0,',','.') }}
          @else -
          @endif
        </td>

        <td>
          <span class="badge
            @if($b->payment_status=='lunas' || $b->payment_status=='paid') badge-lunas
            @elseif($b->payment_status=='pending') badge-pending
            @elseif($b->payment_status=='rejected') badge-rejected
            @endif">
            {{ ucfirst($b->payment_status) }}
          </span>
        </td>

        <!-- Identitas -->
        <td style="text-align:center">
          @php
            $rel = str_replace('public/', '', $b->bukti_identitas ?? '');
            $full = storage_path('app/public/'.$rel);
          @endphp

          @if($rel && file_exists($full))
            <img src="file://{{ $full }}" class="img-bukti">
          @else
            <span style="color:#999">-</span>
          @endif
        </td>

        <!-- Bukti Pembayaran -->
        <td style="text-align:center">
          @php
            $rel2 = str_replace('public/', '', $b->bukti_pembayaran ?? '');
            $full2 = storage_path('app/public/'.$rel2);
          @endphp

          @if($rel2 && file_exists($full2))
            <img src="file://{{ $full2 }}" class="img-bukti">
          @else
            <span style="color:#999">-</span>
          @endif
        </td>

        <td>{{ $b->asal_persyarikatan ?? '-' }}</td>
        <td>{{ $b->no_telp_pic ?? $b->no_telp ?? '-' }}</td>
      </tr>

      @empty
      <tr><td colspan="17" style="text-align:center;color:#999;padding:20px">Tidak ada booking</td></tr>
      @endforelse

      <!-- TOTAL HARGA -->
      @php $grand = $bookings->sum('total_harga'); @endphp
      <tr style="background:#e9e9e9; font-weight:bold;">
        <td colspan="11" style="text-align:right; padding-right:10px;">
          Total Harga Keseluruhan:
        </td>
        <td>
          Rp {{ number_format($grand, 0, ',', '.') }}
        </td>
        <td colspan="5"></td>
      </tr>
    </tbody>
  </table>

  <div class="footer">
    Dokumen ini digenerate otomatis oleh Sistem Penginapan<br>
    © {{ now()->format('Y') }} Sistem Penginapan
  </div>

</body>
</html>
