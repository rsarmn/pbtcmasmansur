<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $meta['title'] ?? 'Laporan Bulanan' }}</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 11px; color: #222; }
    .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #7b1a2e; padding-bottom: 10px; }
    .header h1 { margin: 0; font-size: 18px; color: #7b1a2e; }
    .header p { margin: 4px 0; color: #666; font-size: 10px; }
    
    .stats-grid { display: table; width: 100%; margin: 15px 0; }
    .stat-box { display: table-cell; width: 25%; padding: 10px; text-align: center; border: 1px solid #ddd; background: #f9f9f9; }
    .stat-value { font-size: 20px; font-weight: bold; color: #7b1a2e; }
    .stat-label { font-size: 9px; color: #666; margin-top: 4px; }
    
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th { background: #7b1a2e; color: white; padding: 8px 6px; text-align: left; font-size: 10px; }
    td { border: 1px solid #ddd; padding: 6px; font-size: 10px; }
    tr:nth-child(even) { background: #f9f9f9; }
    
    .footer { margin-top: 20px; font-size: 9px; color: #999; text-align: center; }
    
    .badge { 
      padding: 2px 6px; 
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
  </style>
</head>
<body>
  <div class="header">
    <h1>{{ $meta['title'] ?? 'Laporan Bulanan' }}</h1>
    <p>Periode: {{ $meta['month'] ?? '-' }}</p>
    <p>Dicetak pada: {{ $meta['date'] ?? now()->format('d M Y H:i') }}</p>
  </div>

  <!-- Stats Summary -->
  <div class="stats-grid">
    <div class="stat-box">
      <div class="stat-value">{{ $meta['total_kamar'] ?? 0 }}</div>
      <div class="stat-label">Total Kamar</div>
    </div>
    <div class="stat-box">
      <div class="stat-value">{{ $meta['kamar_kosong'] ?? 0 }}</div>
      <div class="stat-label">Kamar Kosong</div>
    </div>
    <div class="stat-box">
      <div class="stat-value">{{ $meta['kamar_terisi'] ?? 0 }}</div>
      <div class="stat-label">Kamar Terisi</div>
    </div>
    <div class="stat-box">
      <div class="stat-value">{{ $meta['total_booking'] ?? 0 }}</div>
      <div class="stat-label">Total Booking</div>
    </div>
  </div>

  <!-- Booking Table -->
  <h3 style="margin-top: 20px; color: #7b1a2e; font-size: 12px;">Daftar Booking</h3>
  <table>
    <thead>
      <tr>
        <th style="width: 4%;">No</th>
        <th style="width: 18%;">Nama</th>
        <th style="width: 10%;">Jenis</th>
        <th style="width: 12%;">Check-in</th>
        <th style="width: 12%;">Check-out</th>
        <th style="width: 10%;">Kamar</th>
        <th style="width: 12%;">Status</th>
        <th style="width: 12%;">No. Telp</th>
        <th style="width: 10%;">Jml Kamar</th>
      </tr>
    </thead>
    <tbody>
      @forelse($bookings as $i => $b)
      <tr>
        <td style="text-align: center;">{{ $i+1 }}</td>
        <td><strong>{{ $b->nama }}</strong></td>
        <td>
          <span class="badge {{ $b->jenis_tamu == 'Corporate' ? 'badge-corporate' : 'badge-individu' }}">
            {{ $b->jenis_tamu }}
          </span>
        </td>
        <td>{{ \Carbon\Carbon::parse($b->check_in)->format('d M Y') }}</td>
        <td>{{ \Carbon\Carbon::parse($b->check_out)->format('d M Y') }}</td>
        <td>{{ $b->kode_kamar ?? $b->nomor_kamar ?? '-' }}</td>
        <td>
          <span class="badge 
            @if($b->payment_status == 'lunas' || $b->payment_status == 'paid') badge-lunas
            @elseif($b->payment_status == 'pending') badge-pending
            @elseif($b->payment_status == 'rejected') badge-rejected
            @endif">
            {{ $b->payment_status_label }}
          </span>
        </td>
        <td>{{ $b->no_telp_pic ?? $b->no_telp ?? '-' }}</td>
        <td style="text-align: center;">{{ $b->jumlah_kamar ?? 1 }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="9" style="text-align: center; padding: 20px; color: #999;">
          Tidak ada booking pada periode ini
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>

  <!-- Summary Footer -->
  @if($bookings->count() > 0)
  <div style="margin-top: 20px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd;">
    <strong>Ringkasan:</strong><br>
    Total Booking: {{ $bookings->count() }} | 
    Corporate: {{ $bookings->where('jenis_tamu', 'Corporate')->count() }} | 
    Individu: {{ $bookings->where('jenis_tamu', 'Individu')->count() }} |
    Lunas: {{ $bookings->whereIn('payment_status', ['lunas', 'paid'])->count() }}
  </div>
  @endif

  <div class="footer">
    <p>Dokumen ini digenerate secara otomatis oleh Sistem Penginapan</p>
    <p>&copy; {{ now()->format('Y') }} - Sistem Manajemen Penginapan</p>
  </div>
</body>
</html>
