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

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Handle approve confirmation
  document.querySelectorAll('form[action*="approve"]').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      Swal.fire({
        title: 'Konfirmasi Pembayaran?',
        text: 'Booking akan disetujui dan tamu akan mendapat konfirmasi',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Konfirmasi!',
        cancelButtonText: '✗ Batal',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });

  // Handle reject confirmation
  document.querySelectorAll('form[action*="reject"]').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      Swal.fire({
        title: 'Tolak Booking?',
        text: 'Booking akan ditolak dan tamu akan mendapat pemberitahuan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '✗ Ya, Tolak!',
        cancelButtonText: 'Batal',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
});
</script>

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
          <th>Kode Kamar</th>
          <th>Kamar Dipilih</th>
          <th>Kontak</th>
          <th>Status</th>
          <th>Bukti Bayar</th>
          <th>Total Harga</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pengunjungs as $p)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>
            @if(strtolower($p->jenis_tamu) == 'corporate')
              <strong>{{ $p->nama_pic ?? 'PIC' }}</strong>
              @if($p->nama)
                <br><small style="color:#666">{{ $p->nama }}</small>
              @endif
            @else
              <strong>{{ $p->nama }}</strong>
            @endif
          </td>
          <td><span style="background:{{ strtolower($p->jenis_tamu) == 'corporate' ? '#28a745' : '#007bff' }};color:#fff;padding:4px 10px;border-radius:12px;font-size:12px">{{ ucfirst($p->jenis_tamu) }}</span></td>
          <td>{{ $p->check_in }}<br>s/d {{ $p->check_out }}</td>
          <td>{{ $p->kode_kamar ?? '-' }}</td>
          <td>
            @php
              $kamarIds = explode(',', $p->kode_kamar ?? '');
              $jenisKamars = [];
              foreach ($kamarIds as $kamarId) {
                $kamar = \App\Models\Kamar::where('kode_kamar', trim($kamarId))->first();
                if ($kamar && !in_array($kamar->jenis_kamar, $jenisKamars)) {
                  $jenisKamars[] = $kamar->jenis_kamar;
                }
              }
            @endphp
            <strong style="color:#7b1a2e">{{ implode(', ', $jenisKamars) ?: '-' }}</strong>
          </td>
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
              @php
                // normalize path: if stored as 'public/xxx' remove prefix for disk access
                $relPath = \Illuminate\Support\Str::startsWith($p->bukti_pembayaran, 'public/') ? substr($p->bukti_pembayaran, 7) : $p->bukti_pembayaran;
                $fileExists = Storage::disk('public')->exists($relPath);
              @endphp
              @if($fileExists)
                <a href="{{ Storage::url($relPath) }}" target="_blank" style="color:#2563eb;text-decoration:underline">Lihat Bukti</a>
              @else
                <span style="color:#dc2626;">⚠️ File hilang</span>
              @endif
            @else
              <span style="color:#999">Belum upload</span>
            @endif
          </td>
          <td>
            @if($p->total_harga)
              <strong style="color:#10b981">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong>
            @else
              <span style="color:#999">-</span>
            @endif
          </td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
              <form action="{{ route('pengunjung.approve', $p->id) }}" method="POST" style="margin:0">
                @csrf
                <button type="submit" class="btn-confirm" {{ !$p->bukti_pembayaran ? 'disabled title=Butuh bukti pembayaran' : '' }}>
                  Konfirmasi
                </button>
              </form>
              <form action="{{ route('pengunjung.reject', $p->id) }}" method="POST" style="margin:0">
                @csrf
                <button type="submit" class="btn-reject">✗ Tolak</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="11" style="text-align:center;color:#999;padding:32px">Tidak ada pembayaran yang perlu dikonfirmasi</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="table-shell">
  <div style="font-weight:600;margin-bottom:12px;color:#7b1a2e">Upload Bukti Pembayaran (Jika ada yang belum upload)</div>
  <p style="color:#666;font-size:14px;margin-bottom:16px">Gunakan form ini untuk menambahkan bukti pembayaran manual jika tamu mengirim via email/WA</p>
  
  <form action="#" method="POST" enctype="multipart/form-data" id="uploadPaymentForm" style="display:flex;gap:12px;align-items:end">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('uploadPaymentForm');
  if (!form) return;
  form.addEventListener('submit', function(e) {
    const sel = form.querySelector('select[name="booking_id"]');
    const bookingId = sel ? sel.value : '';
    if (!bookingId) {
      e.preventDefault();
      alert('Silakan pilih booking terlebih dahulu');
      return false;
    }
    form.action = '/admin/pengunjung/' + bookingId + '/upload-payment';
  });
});
</script>

@endsection
