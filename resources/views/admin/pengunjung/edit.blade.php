@extends('layout')
@section('content')
<div style="padding:20px">
  <h3>Edit Data Pengunjung: {{ $p->nama }}</h3>
  
  @if($errors->any())
    <div style="background:#fee;padding:12px;border-radius:6px;margin-bottom:16px">
      <strong>Error:</strong>
      <ul style="margin:8px 0 0 20px">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('pengunjung.update', $p->id) }}" method="POST" style="background:#fff;padding:24px;border-radius:8px;max-width:800px">
    @csrf
    
    <div style="margin-bottom:16px">
      <label style="display:block;margin-bottom:4px;font-weight:600">Nama <span style="color:red">*</span></label>
      <input type="text" name="nama" value="{{ old('nama', $p->nama) }}" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
    </div>

    <div style="margin-bottom:16px">
      <label style="display:block;margin-bottom:4px;font-weight:600">Jenis Tamu</label>
      <select name="jenis_tamu" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
        <option value="corporate" {{ old('jenis_tamu', $p->jenis_tamu) == 'corporate' ? 'selected' : '' }}>Corporate</option>
        <option value="individu" {{ old('jenis_tamu', $p->jenis_tamu) == 'individu' ? 'selected' : '' }}>Individu</option>
      </select>
    </div>

    <div style="margin-bottom:16px">
      <label style="display:block;margin-bottom:4px;font-weight:600">No Identitas</label>
      <input type="text" name="no_identitas" value="{{ old('no_identitas', $p->no_identitas) }}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
      <div>
        <label style="display:block;margin-bottom:4px;font-weight:600">Check-in <span style="color:red">*</span></label>
        <input type="date" name="check_in" value="{{ old('check_in', $p->check_in) }}" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
      </div>
      <div>
        <label style="display:block;margin-bottom:4px;font-weight:600">Check-out <span style="color:red">*</span></label>
        <input type="date" name="check_out" value="{{ old('check_out', $p->check_out) }}" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
      </div>
    </div>

    <div style="margin-bottom:16px">
      <label style="display:block;margin-bottom:4px;font-weight:600">Kode Kamar</label>
      @php
        $currentKamars = $p->kode_kamar ? explode(',', str_replace(' ', '', $p->kode_kamar)) : [];
      @endphp
      <select name="kode_kamar[]" multiple style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;min-height:150px">
        @foreach($kamars as $k)
          @php
            $kodeKamar = $k->kode_kamar;
            $isSelected = in_array($kodeKamar, $currentKamars);
            $isAvailable = $k->status === 'kosong' || $isSelected;
          @endphp
          <option 
            value="{{ $kodeKamar }}" 
            {{ $isSelected ? 'selected' : '' }}
            {{ !$isAvailable ? 'disabled' : '' }}
            style="{{ !$isAvailable ? 'color:#999' : ($isSelected ? 'background:#dbeafe;font-weight:600' : '') }}"
          >
            {{ $kodeKamar }} — {{ $k->jenis_kamar }} — {{ $k->gedung }}
            @if(!$isAvailable) (Terisi) @endif
            @if($isSelected) (Terpilih) @endif
          </option>
        @endforeach
      </select>
      <small style="display:block;margin-top:6px;color:#666">
        Tekan <strong>Ctrl</strong> (Windows) atau <strong>Cmd</strong> (Mac) untuk pilih beberapa kamar. 
        <span style="color:#10b981;font-weight:600">{{ $kamars->where('status', 'kosong')->count() }}</span> kamar kosong tersedia.
      </small>
    </div>

    <div style="margin-bottom:16px">
      <label style="display:block;margin-bottom:4px;font-weight:600">Jumlah Kamar</label>
      <input type="number" name="jumlah_kamar" value="{{ old('jumlah_kamar', $p->jumlah_kamar) }}" min="1" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
    </div>

    <div style="margin-bottom:16px">
      <label style="display:block;margin-bottom:4px;font-weight:600">Status Pembayaran</label>
      <select name="payment_status" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
        <option value="pending" {{ old('payment_status', $p->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="konfirmasi_booking" {{ old('payment_status', $p->payment_status) == 'konfirmasi_booking' ? 'selected' : '' }}>Konfirmasi Booking</option>
        <option value="paid" {{ old('payment_status', $p->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
        <option value="lunas" {{ old('payment_status', $p->payment_status) == 'lunas' ? 'selected' : '' }}>Lunas</option>
        <option value="rejected" {{ old('payment_status', $p->payment_status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
      </select>
    </div>

    <div style="margin-bottom:16px">
      <label style="display:block;margin-bottom:4px;font-weight:600">No Telepon</label>
      <input type="text" name="no_telp" value="{{ old('no_telp', $p->no_telp) }}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
    </div>

    <!-- Corporate Fields -->
    <div style="border-top:2px solid #e5e7eb;margin:24px 0;padding-top:24px">
      <h4 style="margin-bottom:16px">Data Corporate (opsional)</h4>
      
      <div style="margin-bottom:16px">
        <label style="display:block;margin-bottom:4px;font-weight:600">Nama Kegiatan</label>
        <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan', $p->nama_kegiatan) }}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
      </div>

      <div style="margin-bottom:16px">
        <label style="display:block;margin-bottom:4px;font-weight:600">Nama PIC</label>
        <input type="text" name="nama_pic" value="{{ old('nama_pic', $p->nama_pic) }}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
      </div>

      <div style="margin-bottom:16px">
        <label style="display:block;margin-bottom:4px;font-weight:600">No HP PIC</label>
        <input type="text" name="no_telp_pic" value="{{ old('no_telp_pic', $p->no_telp_pic) }}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
      </div>

      <div style="margin-bottom:16px">
        <label style="display:block;margin-bottom:4px;font-weight:600">Asal Persyarikatan</label>
        <input type="text" name="asal_persyarikatan" value="{{ old('asal_persyarikatan', $p->asal_persyarikatan) }}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
      </div>

      <div style="margin-bottom:16px">
        <label style="display:block;margin-bottom:4px;font-weight:600">Jumlah Peserta</label>
        <input type="number" name="jumlah_peserta" value="{{ old('jumlah_peserta', $p->jumlah_peserta) }}" min="1" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
      </div>
    </div>

    <!-- Special Requests -->
    <div style="border-top:2px solid #e5e7eb;margin:24px 0;padding-top:24px">
      <h4 style="margin-bottom:16px">Kebutuhan & Request</h4>
      
      <div style="margin-bottom:16px">
        <label style="display:block;margin-bottom:4px;font-weight:600">Special Request</label>
        <textarea name="special_request" rows="3" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">{{ old('special_request', $p->special_request) }}</textarea>
      </div>

      <div style="margin-bottom:16px">
        <label style="display:block;margin-bottom:8px;font-weight:600">Kebutuhan Snack</label>
        <button type="button" class="dropdown-toggle" onclick="toggleDropdown('snack-dropdown')" style="width:100%;padding:12px 16px;background:linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);border:2px solid #dee2e6;border-radius:8px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;font-weight:600;color:#495057">
          <span>Pilih Snack</span>
          <span class="arrow" style="transition:transform 0.3s;font-size:12px">▼</span>
        </button>
        <div id="snack-dropdown" class="dropdown-content" style="display:none;margin-top:8px;padding:12px;background:#fff;border:1px solid #e5e7eb;border-radius:8px">
          @php
            $selectedSnacks = [];
            try {
              $selectedSnacks = is_string($p->kebutuhan_snack) ? json_decode($p->kebutuhan_snack, true) : $p->kebutuhan_snack;
              if (!is_array($selectedSnacks)) $selectedSnacks = [];
            } catch (\Exception $e) {}
            $snackMenus = [
              ['id' => 1, 'nama' => 'Kue Basah (per porsi)', 'harga' => 5000],
              ['id' => 3, 'nama' => 'Pisang Goreng (per porsi)', 'harga' => 8000],
              ['id' => 4, 'nama' => 'Roti Bakar', 'harga' => 10000],
              ['id' => 5, 'nama' => 'Kue Kering', 'harga' => 15000]
            ];
          @endphp
          @foreach($snackMenus as $menu)
            @php
              $checked = false;
              $currentPorsi = 0;
              foreach($selectedSnacks as $s) {
                if(isset($s['menu_id']) && $s['menu_id'] == $menu['id']) {
                  $checked = true;
                  $currentPorsi = $s['porsi'] ?? 0;
                  break;
                }
              }
            @endphp
            <div style="padding:10px;margin-bottom:8px;background:#f8f9fa;border-radius:6px">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="checkbox" 
                       class="snack-checkbox" 
                       data-menu-id="{{ $menu['id'] }}" 
                       data-nama="{{ $menu['nama'] }}" 
                       data-harga="{{ $menu['harga'] }}"
                       {{ $checked ? 'checked' : '' }}
                       onchange="updateSnackSelection()"
                       style="width:18px;height:18px">
                <span style="flex:1;font-weight:600">{{ $menu['nama'] }}</span>
                <span style="color:#666">Rp {{ number_format($menu['harga'], 0, ',', '.') }}</span>
              </label>
              <div class="porsi-input" style="margin-top:8px;margin-left:26px;{{ $checked ? '' : 'display:none' }}">
                <label style="font-size:13px;color:#666;margin-right:8px">Jumlah Porsi:</label>
                <input type="number" 
                       class="porsi-{{ $menu['id'] }}" 
                       value="{{ $currentPorsi }}" 
                       min="1" 
                       onchange="updateSnackSelection()"
                       style="width:80px;padding:6px;border:1px solid #ddd;border-radius:4px">
              </div>
            </div>
          @endforeach
        </div>
        <input type="hidden" name="kebutuhan_snack" id="snack-input" value="{{ old('kebutuhan_snack', $p->kebutuhan_snack) }}">
      </div>

      <div style="margin-bottom:16px">
        <label style="display:block;margin-bottom:8px;font-weight:600">Kebutuhan Makan</label>
        <button type="button" class="dropdown-toggle" onclick="toggleDropdown('makan-dropdown')" style="width:100%;padding:12px 16px;background:linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);border:2px solid #dee2e6;border-radius:8px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;font-weight:600;color:#495057">
          <span>Pilih Makan</span>
          <span class="arrow" style="transition:transform 0.3s;font-size:12px">▼</span>
        </button>
        <div id="makan-dropdown" class="dropdown-content" style="display:none;margin-top:8px;padding:12px;background:#fff;border:1px solid #e5e7eb;border-radius:8px">
          @php
            $selectedMeals = [];
            try {
              $selectedMeals = is_string($p->kebutuhan_makan) ? json_decode($p->kebutuhan_makan, true) : $p->kebutuhan_makan;
              if (!is_array($selectedMeals)) $selectedMeals = [];
            } catch (\Exception $e) {}
            $makanMenus = [
              ['id' => 6, 'nama' => 'Nasi Box Premium', 'harga' => 30000],
              ['id' => 8, 'nama' => 'Prasmanan (per orang)', 'harga' => 35000],
              ['id' => 9, 'nama' => 'Nasi Box Standar', 'harga' => 25000],
              ['id' => 10, 'nama' => 'Paket Makan Siang', 'harga' => 28000]
            ];
          @endphp
          @foreach($makanMenus as $menu)
            @php
              $checked = false;
              $currentPorsi = 0;
              foreach($selectedMeals as $m) {
                if(isset($m['menu_id']) && $m['menu_id'] == $menu['id']) {
                  $checked = true;
                  $currentPorsi = $m['porsi'] ?? 0;
                  break;
                }
              }
            @endphp
            <div style="padding:10px;margin-bottom:8px;background:#f8f9fa;border-radius:6px">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="checkbox" 
                       class="makan-checkbox" 
                       data-menu-id="{{ $menu['id'] }}" 
                       data-nama="{{ $menu['nama'] }}" 
                       data-harga="{{ $menu['harga'] }}"
                       {{ $checked ? 'checked' : '' }}
                       onchange="updateMakanSelection()"
                       style="width:18px;height:18px">
                <span style="flex:1;font-weight:600">{{ $menu['nama'] }}</span>
                <span style="color:#666">Rp {{ number_format($menu['harga'], 0, ',', '.') }}</span>
              </label>
              <div class="porsi-input" style="margin-top:8px;margin-left:26px;{{ $checked ? '' : 'display:none' }}">
                <label style="font-size:13px;color:#666;margin-right:8px">Jumlah Porsi:</label>
                <input type="number" 
                       class="porsi-{{ $menu['id'] }}" 
                       value="{{ $currentPorsi }}" 
                       min="1" 
                       onchange="updateMakanSelection()"
                       style="width:80px;padding:6px;border:1px solid #ddd;border-radius:4px">
              </div>
            </div>
          @endforeach
        </div>
        <input type="hidden" name="kebutuhan_makan" id="makan-input" value="{{ old('kebutuhan_makan', $p->kebutuhan_makan) }}">
      </div>
    </div>

    <div style="display:flex;gap:12px;margin-top:24px">
      <button type="submit" class="pill-btn" style="background:#2563eb;color:#fff;padding:12px 32px;border:none;cursor:pointer">
        Simpan Perubahan
      </button>
      <a href="{{ route('pengunjung.show', $p->id) }}" class="pill-btn" style="background:#6b7280;color:#fff;padding:12px 32px">
        Batal
      </a>
    </div>
  </form>
</div>

<script>
function toggleDropdown(id) {
  const dropdown = document.getElementById(id);
  const toggle = dropdown.previousElementSibling;
  const arrow = toggle.querySelector('.arrow');
  
  dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
  arrow.style.transform = dropdown.style.display === 'none' ? 'rotate(0deg)' : 'rotate(180deg)';
}

function updateSnackSelection() {
  const checkboxes = document.querySelectorAll('.snack-checkbox');
  const selected = [];
  
  checkboxes.forEach(cb => {
    const parent = cb.closest('div').parentElement;
    const porsiInput = parent.querySelector('.porsi-input');
    
    if (cb.checked) {
      if (porsiInput) porsiInput.style.display = 'block';
      const porsiField = parent.querySelector('.porsi-' + cb.dataset.menuId);
      const porsi = porsiField ? parseInt(porsiField.value) || 1 : 1;
      
      selected.push({
        menu_id: parseInt(cb.dataset.menuId),
        nama: cb.dataset.nama,
        harga: parseInt(cb.dataset.harga),
        porsi: porsi
      });
    } else {
      if (porsiInput) porsiInput.style.display = 'none';
    }
  });
  
  document.getElementById('snack-input').value = JSON.stringify(selected);
}

function updateMakanSelection() {
  const checkboxes = document.querySelectorAll('.makan-checkbox');
  const selected = [];
  
  checkboxes.forEach(cb => {
    const parent = cb.closest('div').parentElement;
    const porsiInput = parent.querySelector('.porsi-input');
    
    if (cb.checked) {
      if (porsiInput) porsiInput.style.display = 'block';
      const porsiField = parent.querySelector('.porsi-' + cb.dataset.menuId);
      const porsi = porsiField ? parseInt(porsiField.value) || 1 : 1;
      
      selected.push({
        menu_id: parseInt(cb.dataset.menuId),
        nama: cb.dataset.nama,
        harga: parseInt(cb.dataset.harga),
        porsi: porsi
      });
    } else {
      if (porsiInput) porsiInput.style.display = 'none';
    }
  });
  
  document.getElementById('makan-input').value = JSON.stringify(selected);
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
  updateSnackSelection();
  updateMakanSelection();
});
</script>
@endsection
