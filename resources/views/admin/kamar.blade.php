@extends('layout')
@section('content')
<style>
  .section-header{background:var(--brand);color:#fff;border-radius:18px;padding:18px 22px;align-items:center;justify-content:space-between;margin-bottom:14px}
  .pill-btn{background:#fff;color:var(--brand);border:0;border-radius:999px;padding:8px 16px;font-weight:700;text-decoration:none}
  .pill-btn:hover{filter:brightness(95%)}
  .table-shell{background:rgba(179,18,59,.08); border-radius:18px; padding:18px}
  .data-table{width:100%;border-collapse:collapse}
  .data-table thead th{background:#f5d7de;padding:12px;text-align:left;color:#7b1a2e;font-weight:600;border-bottom:2px solid #e8c5d0}
  .data-table tbody td{background:#fff;padding:12px;border-bottom:1px solid #f0e1e5}
  .data-table tbody tr:last-child td{border-bottom:0}
  .btn-delete{background:#dc3545;color:#fff;border:0;padding:6px 12px;border-radius:6px;font-size:13px;cursor:pointer}
  .btn-delete:hover{background:#c82333}
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
  <a href="{{ route('admin.dashboard') }}" class="pill-btn">Kembali</a>
</div> --}}

<div class="table-shell">
  <div class="flex items-center justify-between mb-3">
    <div style="font-weight:600;color:#7b1a2e">DATA KAMAR PENGINAPAN</div>
    <div class="flex items-center gap-2">
      <a href="{{ route('kamar.create') }}" class="px-3 py-1 text-sm bg-[var(--brand)] text-white rounded">Tambah Kamar</a>
    </div>
  </div>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>Kode Kamar</th>
          <th>Jenis Kamar</th>
          <th>Gedung</th>
          <th>Harga</th>
          <th>Fasilitas</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($kamars as $k)
        <tr>
          <td>{{ $k->kode_kamar ?? $k->nomor_kamar }}</td>
          <td>{{ $k->jenis_kamar }}</td>
          <td>{{ $k->gedung }}</td>
          <td>Rp {{ number_format($k->harga ?? 0,0,',','.') }}</td>
          <td>{{ $k->fasilitas }}</td>
          <td><span class="px-2 py-1 rounded text-xs" style="background:{{ $k->status == 'kosong' ? '#d1fae5' : '#fee2e2' }};color:{{ $k->status == 'kosong' ? '#065f46' : '#991b1b' }}">{{ ucfirst($k->status) }}</span></td>
          <td>
            <div style="position:relative;display:inline-block">
              <button class="pill-btn" style="font-size:13px">Aksi ▾</button>
              <div style="position:absolute;right:0;background:#fff;border:1px solid #eee;padding:8px;border-radius:6px;display:none;min-width:140px;z-index:100" class="aksi-menu">
                <a href="{{ route('kamar.edit', $k->id) }}" style="display:block;padding:6px 8px;text-decoration:none;color:#333">Edit</a>
                <a href="#" onclick="alert('View Detail Kamar: {{ $k->kode_kamar ?? $k->nomor_kamar }}')" style="display:block;padding:6px 8px;text-decoration:none;color:#333">View Detail</a>
                <form action="{{ route('kamar.destroy', $k->id) }}" method="GET" onsubmit="return confirm('Hapus kamar ini?')" style="margin:0">
                  <button type="submit" style="width:100%;text-align:left;padding:6px 8px;border:0;background:none;cursor:pointer;color:#dc3545">Hapus</button>
                </form>
              </div>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:#999;padding:24px">Belum ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
