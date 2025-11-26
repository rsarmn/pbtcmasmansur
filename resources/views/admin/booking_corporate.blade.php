@extends('layout')
@section('content')
<div class="container mt-5">
  <h3>Form Booking Corporate</h3>
  <form action="{{ route('booking.storeCorporate') }}" method="POST">@csrf
    <div class="row">
      <div class="col-md-6">
        <label>Nama Kegiatan</label>
        <input name="nama_kegiatan" class="form-control" required>
        <label class="mt-2">Nama PIC</label>
        <input name="nama_pic" class="form-control">
        <label class="mt-2">No Telp PIC</label>
        <input name="no_telp_pic" class="form-control">
        <label class="mt-2">Tanggal</label>
        <input type="date" name="check_in" class="form-control">
        <input type="date" name="check_out" class="form-control mt-2">

        <label class="mt-3">Jumlah Pengunjung</label>
        <input type="number" name="jumlah_pengunjung" min="1" class="form-control" placeholder="Total pengunjung (opsional)">
      </div>

      <div class="col-md-6">
        <label>Jumlah Peserta</label>
        <input name="jumlah_peserta" class="form-control">
        <label class="mt-2">Jumlah Kamar (akan otomatis terisi saat pilih kamar)</label>
        <input id="jumlah_kamar_input" name="jumlah_kamar" class="form-control" readonly>

        <label class="mt-3">Pilih Kamar</label>
        <div style="display:flex;gap:8px;margin-top:6px;margin-bottom:12px;align-items:center;">
          <input id="kamar-search" placeholder="Cari kode kamar (mis. KUN-STD-01)" style="flex:1;padding:8px;border:1px solid #e5e7eb;border-radius:8px;" />
          <button type="button" id="clear-search" style="padding:8px 12px;background:#efefef;border:1px solid #e5e7eb;border-radius:8px;">Bersihkan</button>
        </div>
        @if(isset($kamars) && count($kamars) > 0)
          <div class="d-flex flex-wrap" id="kamar-blocks" style="gap:10px;">
            @foreach($kamars as $k)
              @php $isDisabled = $k->status !== 'kosong'; @endphp
              <label class="kamar-block" data-status="{{ $k->status ?? 'kosong' }}" style="border:1px solid #ddd;padding:10px;border-radius:8px;min-width:140px;cursor:pointer;display:flex;flex-direction:column;align-items:flex-start;background:{{ $isDisabled ? '#f3f4f6' : '#fff' }};opacity:{{ $isDisabled ? '0.6' : '1' }}">
                <div style="display:flex;justify-content:space-between;width:100%">
                  <div style="font-weight:700">{{ $k->kode_kamar }}</div>
                  <div style="font-size:12px;color:#666">{{ $k->jenis_kamar }}</div>
                </div>
                <div style="font-size:12px;color:#444;margin-top:6px">Rp {{ number_format($k->harga ?? 0,0,',','.') }}</div>
                <div style="margin-top:8px;display:flex;gap:8px;align-items:center;width:100%">
                  <input type="checkbox" name="kode_kamar[]" value="{{ $k->kode_kamar }}" {{ $isDisabled ? 'disabled' : '' }} class="kamar-checkbox" data-kode="{{ $k->kode_kamar }}">
                  <small style="color:{{ $isDisabled ? '#9ca3af' : '#10b981' }}">{{ $isDisabled ? 'Terisi' : 'Pilih' }}</small>
                </div>
              </label>
            @endforeach
          </div>
        @else
          <p class="text-muted">Daftar kamar tidak tersedia. Pastikan controller mengirim variabel <code>$kamars</code>.</p>
        @endif

        <label class="mt-3">Kebutuhan konsumsi - Snack <small style="color:#666">(kosong jika tidak)</small></label>
        <input name="kebutuhan_snack" class="form-control" placeholder="jumlah porsi atau keterangan">
        <label class="mt-2">Kebutuhan konsumsi - Makan <small style="color:#666">(kosong jika tidak)</small></label>
        <input name="kebutuhan_makan" class="form-control" placeholder="jumlah porsi atau keterangan">
        <label class="mt-2">Special Request</label>
        <textarea name="special_request" class="form-control"></textarea>
      </div>
    </div>
    <button class="btn btn-primary mt-3">Simpan Booking</button>
  </form>

  <style>
    .kamar-block input[type="checkbox"] { transform: scale(1.1); }
    .kamar-block:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const container = document.getElementById('kamar-blocks');
      const checkboxes = Array.from(document.querySelectorAll('.kamar-checkbox'));
      const jumlahInput = document.getElementById('jumlah_kamar_input');
      const searchInput = document.getElementById('kamar-search');
      const clearBtn = document.getElementById('clear-search');

      function isVisible(el) {
        return el.offsetParent !== null;
      }

      function updateJumlah() {
        const count = checkboxes.filter(c => c.checked && isVisible(c.closest('.kamar-block'))).length;
        jumlahInput.value = count;
      }

      function reorderKamarBlocks() {
        if (!container) return;
        const blocks = Array.from(container.querySelectorAll('.kamar-block'));
        // Keep only visible blocks when reordering so search results keep relative order
        const visible = blocks.filter(b => isVisible(b));
        const hidden = blocks.filter(b => !isVisible(b));

        visible.sort((a, b) => {
          const sa = (a.getAttribute('data-status') || '').toLowerCase();
          const sb = (b.getAttribute('data-status') || '').toLowerCase();
          // put anything not 'kosong' (i.e., 'terisi') before kosong
          const aPriority = sa === 'kosong' ? 1 : 0;
          const bPriority = sb === 'kosong' ? 1 : 0;
          if (aPriority !== bPriority) return aPriority - bPriority;
          // fallback: keep DOM order (stable sort)
          return 0;
        });

        // Append in order: visible (sorted) then hidden (original order)
        visible.concat(hidden).forEach(n => container.appendChild(n));
      }

      function filterKamar() {
        const q = (searchInput.value || '').trim().toLowerCase();
        const blocks = Array.from(container.querySelectorAll('.kamar-block'));
        blocks.forEach(b => {
          const cb = b.querySelector('.kamar-checkbox');
          const kode = (cb && cb.dataset.kode ? cb.dataset.kode : '').toLowerCase();
          if (!q) {
            b.style.display = '';
          } else {
            if (kode.indexOf(q) !== -1) {
              b.style.display = '';
            } else {
              b.style.display = 'none';
            }
          }
        });
        reorderKamarBlocks();
        updateJumlah();
      }

      // Wire events
      checkboxes.forEach(cb => cb.addEventListener('change', updateJumlah));
      searchInput.addEventListener('input', filterKamar);
      clearBtn.addEventListener('click', function() { searchInput.value = ''; filterKamar(); });

      // Initial ordering: move terisi to top
      reorderKamarBlocks();
      updateJumlah();
    });
  </script>
</div>
@endsection
