@extends('layout')
@section('content')
<style>
  .section-header{background:var(--brand);color:#fff;border-radius:18px;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
  .pill-btn{background:#fff;color:var(--brand);border:0;border-radius:999px;padding:8px 16px;font-weight:700;text-decoration:none}
  .pill-btn:hover{filter:brightness(95%)}
  .table-shell{background:rgba(179,18,59,.08); border-radius:18px; padding:18px}
  .data-table{width:100%;border-collapse:collapse}
  .data-table thead th{background:#f5d7de;padding:12px;text-align:left;color:#7b1a2e;font-weight:600;border-bottom:2px solid #e8c5d0}
  .data-table tbody td{background:#fff;padding:12px;border-bottom:1px solid #f0e1e5}
  .data-table tbody tr:last-child td{border-bottom:0}
  .btn-delete{background:#dc3545;color:#fff;border:0;padding:6px 12px;border-radius:6px;font-size:13px;cursor:pointer}
  .btn-delete:hover{background:#c82333}
  .aksi-menu{z-index:100}
  .aksi-menu.show{display:block!important}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.pill-btn').forEach(btn => {
    if(btn.textContent.includes('Aksi')) {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const menu = this.nextElementSibling;
        document.querySelectorAll('.aksi-menu').forEach(m => m.classList.remove('show'));
        menu.classList.toggle('show');
      });
    }
  });
  document.addEventListener('click', () => {
    document.querySelectorAll('.aksi-menu').forEach(m => m.classList.remove('show'));
  });
});
</script>

{{-- <div class="section-header">
  <div class="flex items-center gap-2">
    <a href="{{ route('booking.pdf') }}" class="pill-btn" target="_blank" rel="noopener">Unduh PDF</a>
    <a href="{{ route('admin.dashboard') }}" class="pill-btn">Kembali</a>
  </div>
</div> --}}

<div class="table-shell">
  <div style="font-weight:600;margin-bottom:12px;color:#7b1a2e">DATA PENGUNJUNG PENGINAPAN</div>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Identitas</th>
          <th>No Identitas</th>
          <th>Jenis Tamu</th>
          <th>Check-in</th>
          <th>Check-out</th>
          <th>Kode Kamar</th>
          <th>Bayar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pengunjungs as $p)
        <tr>
          <td>{{ $p->nama }}</td>
          <td>KTP</td>
          <td>{{ $p->no_identitas }}</td>
          <td>{{ $p->jenis_tamu }}</td>
          <td>{{ $p->check_in }}</td>
          <td>{{ $p->check_out }}</td>
          <td>{{ $p->kode_kamar ?? $p->nomor_kamar }}</td>
          <td>{{ $p->payment_status_label }}</td>
          <td>
            @php
              $phone = preg_replace('/[^0-9+]/','',$p->no_telp_pic ?? $p->no_telp ?? '');
              $wa = $phone ? 'https://wa.me/'.ltrim($phone,'0') : null;
            @endphp
            <div style="display:flex;gap:6px;align-items:center">
              @if($wa)
                <a href="{{ $wa }}" target="_blank" class="pill-btn" style="background:#25D366;color:#fff;padding:6px 10px">Chat</a>
              @endif
              <div style="position:relative">
                <button class="pill-btn">Aksi ▾</button>
                <div style="position:absolute;right:0;background:#fff;border:1px solid #eee;padding:8px;border-radius:6px;display:none;min-width:160px" class="aksi-menu">
                  <a href="{{ route('pengunjung.show', $p->id) }}" style="display:block;padding:6px 8px">View Detail</a>
                  <a href="{{ route('pengunjung.edit', $p->id) }}" style="display:block;padding:6px 8px">Edit</a>
                  <form action="{{ route('pengunjung.destroy', $p->id) }}" method="GET" onsubmit="return confirm('Hapus pengunjung ini?')">
                    <button style="width:100%;text-align:left;padding:6px 8px;border:0;background:none;cursor:pointer;color:#dc3545">Hapus</button>
                  </form>
                </div>
              </div>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;color:#999;padding:24px">Belum ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
