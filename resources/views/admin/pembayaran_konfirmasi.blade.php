@extends('layout')
@section('content')
<style>
  .section-header{background:var(--brand);color:#fff;border-radius:18px;padding:18px 22px;margin-bottom:14px}
  .pill-btn{background:#fff;color:var(--brand);border:0;border-radius:999px;padding:8px 16px;font-weight:700;text-decoration:none;display:inline-block}
  .pill-btn:hover{filter:brightness(95%)}
  .table-shell{background:rgba(179,18,59,.08); border-radius:18px; padding:18px;margin-bottom:20px}
  .data-table{width:100%;border-collapse:collapse}
  .data-table thead th{background:#f5d7de;padding:12px;text-align:left;color:#7b1a2e;font-weight:600;border-bottom:2px solid #e8c5d0}
  .data-table tbody td{background:#fff;padding:12px;border-bottom:1px solid #f0e1e5}
  .btn-confirm{background:#10b981;color:#fff;border:0;padding:8px 16px;border-radius:6px;font-size:13px;cursor:pointer;font-weight:600}
  .btn-confirm:hover{background:#059669}
  .btn-reject{background:#ef4444;color:#fff;border:0;padding:8px 16px;border-radius:6px;font-size:13px;cursor:pointer;font-weight:600}
  .btn-reject:hover{background:#dc2626}
  .status-badge{padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600}
</style>

<div class="section-header">
  <div style="display:flex;justify-content:space-between;align-items:center;width:100%">
    <h2 style="margin:0;font-size:20px">Konfirmasi Pembayaran</h2>
    <a href="{{ route('admin.dashboard') }}" class="pill-btn">Kembali</a>
  </div>
</div>

<div class="table-shell">
  <div style="font-weight:600;margin-bottom:16px;color:#7b1a2e;font-size:16px">Daftar Pembayaran Menunggu Konfirmasi</div>
  
  @if(session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:12px;border-radius:8px;margin-bottom:16px">
      {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:16px">
      @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  <div style="overflow-x:auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Tamu</th>
          <th>Jenis</th>
          <th>Check-in / Check-out</th>
          <th>Kamar Dipilih</th>
          <th>Kode Kamar</th>
          <th>Kontak</th>
          <th>Status</th>
          <th>Bukti Bayar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pengunjungs as $p)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td><strong>{{ $p->nama }}</strong>
            @if($p->jenis_tamu == 'Corporate')
              <br><small>PIC: {{ $p->nama_pic }}</small>
            @endif
          </td>
          <td>{{ $p->jenis_tamu }}</td>
          <td>{{ $p->check_in }}<br>s/d {{ $p->check_out }}</td>
          <td>
            @php
              // Extract jenis_kamar from special_request field
              preg_match('/Jenis Kamar: ([^\n]+)/', $p->special_request, $match);
              $jenis_kamar_pilihan = $match[1] ?? 'Tidak ada';
            @endphp
            <strong style="color:#7b1a2e">{{ $jenis_kamar_pilihan }}</strong>
          </td>
          <td>{{ $p->kode_kamar ?? $p->nomor_kamar ?? '-' }}</td>
          <td>
            @php
              $phone = preg_replace('/[^0-9+]/','',$p->no_telp_pic ?? $p->no_telp ?? '');
              $wa = $phone ? 'https://wa.me/'.ltrim($phone,'0') : null;
            @endphp
            @if($wa)
              <a href="{{ $wa }}" target="_blank" style="background:#25D366;color:#fff;padding:4px 8px;border-radius:4px;text-decoration:none;font-size:12px">💬 Chat</a>
            @else
              {{ $p->no_telp ?? '-' }}
            @endif
          </td>
          <td>
            <span class="status-badge" style="background:#fef3c7;color:#92400e">
              {{ $p->payment_status_label }}
            </span>
          </td>
          <td>
            @if($p->bukti_pembayaran)
              @php $fileExists = file_exists(storage_path('app/' . $p->bukti_pembayaran)); @endphp
              @if($fileExists)
                <a href="{{ Storage::url(str_replace('public/','',$p->bukti_pembayaran)) }}" target="_blank" style="color:#2563eb;text-decoration:underline">Lihat Bukti</a>
              @else
                <span style="color:#dc2626;">⚠️ File hilang</span>
              @endif
            @else
              <span style="color:#999">Belum upload</span>
            @endif
          </td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
              <form action="{{ route('pengunjung.approve', $p->id) }}" method="POST" style="margin:0">
                @csrf
                <button type="submit" class="btn-confirm" {{ !$p->bukti_pembayaran ? 'disabled title=Butuh bukti pembayaran' : '' }}>
                  ✓ Konfirmasi
                </button>
              </form>
              <form action="{{ route('pengunjung.reject', $p->id) }}" method="POST" onsubmit="return confirm('Yakin tolak booking ini?')" style="margin:0">
                @csrf
                <button type="submit" class="btn-reject">✗ Tolak</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;color:#999;padding:32px">Tidak ada pembayaran yang perlu dikonfirmasi</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="table-shell">
  <div style="font-weight:600;margin-bottom:12px;color:#7b1a2e">Upload Bukti Pembayaran (Jika ada yang belum upload)</div>
  <p style="color:#666;font-size:14px;margin-bottom:16px">Gunakan form ini untuk menambahkan bukti pembayaran manual jika tamu mengirim via email/WA</p>
  
  <form action="{{ route('pengunjung.upload_payment', $pengunjungs->first()->id ?? 1) }}" method="POST" enctype="multipart/form-data" style="display:flex;gap:12px;align-items:end">
    @csrf
    <div>
      <label style="display:block;margin-bottom:4px;font-size:14px;font-weight:600">Pilih Booking:</label>
      <select name="booking_id" required style="padding:8px 12px;border:1px solid #ddd;border-radius:6px;min-width:200px">
        <option value="">-- Pilih --</option>
        @foreach($pengunjungs as $p)
          <option value="{{ $p->id }}">{{ $p->nama }} - {{ $p->check_in }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label style="display:block;margin-bottom:4px;font-size:14px;font-weight:600">File Bukti:</label>
      <input type="file" name="bukti_pembayaran" required accept=".jpg,.jpeg,.png,.pdf" style="padding:8px;border:1px solid #ddd;border-radius:6px">
    </div>
    <button type="submit" class="pill-btn" style="background:var(--brand);color:#fff">Upload</button>
  </form>
</div>

@endsection
