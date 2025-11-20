@extends('layout')
@section('content')
<style>
  :root {
    --primary: #3b82f6;
    --primary-dark: #2563eb;
    --success: #10b981;
    --danger: #ef4444;
    --warning: #f59e0b;
    --gray: #6b7280;
    --light: #f9fafb;
  }

  .report-container {
    padding: 32px 24px;
    max-width: 1400px;
    margin: auto;
  }

  .report-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
  }

  .report-header h2 {
    color: #1f2937;
    font-size: 24px;
    margin: 0;
  }

  .report-header p {
    color: var(--gray);
    margin-top: 4px;
    font-size: 14px;
  }

  .month-selector {
    display: flex;
    gap: 12px;
    align-items: center;
  }

  .month-selector select {
    padding: 10px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    font-size: 14px;
    outline: none;
    transition: border 0.2s;
  }

  .month-selector select:focus {
    border-color: var(--primary);
  }

  .month-selector button {
    background: var(--primary);
    color: white;
    border: none;
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s;
  }

  .month-selector button:hover {
    background: var(--primary-dark);
  }

  .btn-export {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    font-weight: 600;
    font-size: 14px;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
  }

  .btn-pdf { background: var(--danger); color: #fff; }
  .btn-pdf:hover { background: #dc2626; }
  .btn-csv { background: var(--success); color: #fff; }
  .btn-csv:hover { background: #059669; }
  .btn-back { background: var(--gray); color: #fff; }
  .btn-back:hover { background: #4b5563; }

  .export-buttons {
    display: flex;
    gap: 10px;
    margin-bottom: 24px;
  }

  .stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
  }

  .stat-card {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    border-left: 5px solid var(--primary);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }

  .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
  }

  .stat-label {
    font-size: 14px;
    color: var(--gray);
    margin-top: 6px;
  }

  .table-wrapper {
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  }

  .data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
  }

  .data-table thead {
    background: var(--light);
  }

  .data-table th {
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
  }

  .data-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
  }

  .data-table tbody tr:hover {
    background: #f9fafb;
  }

  .empty-state {
    text-align: center;
    padding: 40px 0;
    color: #9ca3af;
  }

  .empty-state div:first-child {
    font-size: 48px;
  }
</style>

<div class="report-container">
  <!-- Header -->
  <div class="report-header">
    <div>
      <h2 class="text-bold">Unduh Laporan Bulanan</h2>
      <p>{{ \Carbon\Carbon::parse($monthStart)->format('F Y') }}</p>
    </div>

    <div class="month-selector">
      <form method="GET" action="{{ route('report.monthly') }}" style="display:flex; gap:12px;">
        <select name="month" required>
          <option value="">-- Pilih Bulan --</option>
          @for($i = 0; $i < 12; $i++)
            @php
              $date = now()->subMonths($i);
              $value = $date->format('Y-m');
              $selected = ($selectedMonth == $value) ? 'selected' : '';
            @endphp
            <option value="{{ $value }}" {{ $selected }}>{{ $date->format('F Y') }}</option>
          @endfor
        </select>
        <button type="submit">Tampilkan</button>
      </form>
    </div>
  </div>

  <!-- Export Buttons -->
  <div class="export-buttons">
    <a href="{{ route('report.monthly.pdf', ['month' => $selectedMonth]) }}" class="btn-export btn-pdf">Export PDF</a>
    <a href="{{ route('report.monthly.csv', ['month' => $selectedMonth]) }}" class="btn-export btn-csv">Export CSV</a>
  </div>

  <!-- Stats -->
  <div class="stat-grid">
    <div class="stat-card" style="border-left-color:#3b82f6;">
      <div class="stat-value">{{ $totalKamar }}</div>
      <div class="stat-label">Total Kamar</div>
    </div>
    <div class="stat-card" style="border-left-color:#10b981;">
      <div class="stat-value">{{ $kamarKosong }}</div>
      <div class="stat-label">Kamar Kosong</div>
    </div>
    <div class="stat-card" style="border-left-color:#f59e0b;">
      <div class="stat-value">{{ $kamarTerisi }}</div>
      <div class="stat-label">Kamar Terisi</div>
    </div>
    <div class="stat-card" style="border-left-color:#8b5cf6;">
      <div class="stat-value">{{ $bookingsThisMonth->count() }}</div>
      <div class="stat-label">Booking Bulan Ini</div>
    </div>
  </div>

  <!-- Table -->
  <div class="table-wrapper">
    <h3 style="color:#1f2937; margin-bottom:16px;">Daftar Booking Bulan Ini</h3>
    <div style="overflow-x:auto;">
      <table class="data-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Jenis Tamu</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Kamar</th>
            <th>Status Pembayaran</th>
            <th>No. Telp</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bookingsThisMonth as $i => $b)
          <tr>
            <td>{{ $i+1 }}</td>
            <td><strong>{{ $b->nama }}</strong></td>
            <td>
              <span style="padding:4px 8px;border-radius:4px;
                background:{{ $b->jenis_tamu == 'Corporate' ? '#dbeafe' : '#fef3c7' }};
                color:{{ $b->jenis_tamu == 'Corporate' ? '#1e40af' : '#92400e' }};
                font-size:12px; font-weight:600;">
                {{ $b->jenis_tamu }}
              </span>
            </td>
            <td>{{ \Carbon\Carbon::parse($b->check_in)->format('d M Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($b->check_out)->format('d M Y') }}</td>
            <td>{{ $b->kode_kamar ?? $b->nomor_kamar ?? '-' }}</td>
            <td>
              <span style="padding:4px 8px;border-radius:4px;font-size:12px;font-weight:600;
                @if($b->payment_status == 'lunas' || $b->payment_status == 'paid') background:#d1fae5;color:#065f46;
                @elseif($b->payment_status == 'pending') background:#fef3c7;color:#92400e;
                @elseif($b->payment_status == 'rejected') background:#fee2e2;color:#991b1b;
                @else background:#e5e7eb;color:#374151;
                @endif">
                {{ $b->payment_status_label }}
              </span>
            </td>
            <td>{{ $b->no_telp_pic ?? $b->no_telp ?? '-' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="empty-state">
              <div>📭</div>
              <div>Tidak ada booking pada bulan ini</div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
