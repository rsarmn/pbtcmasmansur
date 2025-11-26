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

  <form action="{{ route('pengunjung.update', $p->id) }}" method="POST" style="background:#fff;padding:24px;border-radius:8px;max-width:800px" id="editForm">
    @csrf
    
    <div style="margin-bottom:16px">
      <label style="display:block;margin-bottom:4px;font-weight:600">Nama PIC <span style="color:red">*</span></label>
      <input type="text" name="nama_pic" value="{{ old('nama_pic', $p->nama_pic ?: $p->nama) }}" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
      <input type="hidden" name="nama" value="{{ $p->nama_pic ?: $p->nama }}">
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

    <div style="margin-bottom:16px">
      <label style="display:block;margin-bottom:4px;font-weight:600">No HP PIC</label>
      <input type="text" name="no_telp_pic" value="{{ old('no_telp_pic', $p->no_telp_pic ?: $p->no_telp) }}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
      <input type="hidden" name="no_telp" value="{{ $p->no_telp_pic ?: $p->no_telp }}">
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

    <div style="border-top:2px solid #e5e7eb;margin:24px 0;padding-top:24px">
      <h4 style="margin-bottom:16px">Data Kegiatan</h4>
      
      <div style="margin-bottom:16px">
        <label style="display:block;margin-bottom:4px;font-weight:600">Nama Kegiatan</label>
        <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan', $p->nama_kegiatan) }}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
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

    <div style="border-top:2px solid #e5e7eb;margin:24px 0;padding-top:24px">
      <h4 style="margin-bottom:16px">Kebutuhan & Request</h4>
      
      <div style="margin-bottom:16px">
        <label style="display:block;margin-bottom:4px;font-weight:600">Special Request</label>
        <textarea name="special_request" rows="3" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">{{ old('special_request', $p->special_request) }}</textarea>
      </div>

      <div style="margin-bottom:16px">
        <label style="display:block;margin-bottom:8px;font-weight:600">Kebutuhan Snack</label>
        @php
          $selectedSnacks = [];
          $raw = $p->kebutuhan_snack ?? '';
          if (is_string($raw) && $raw !== '') {
            // Try JSON decode first
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
              $first = reset($decoded);
              if (is_string($first)) {
                // Array of strings: ["Kue Basah", "Roti Bakar"]
                $selectedSnacks = array_filter($decoded, fn($v) => is_string($v) && $v !== '');
              } elseif (is_array($first) && isset($first['nama'])) {
                // Array of objects: [{"nama": "Kue Basah"}, ...]
                $selectedSnacks = array_column($decoded, 'nama');
              }
            } else {
              // Not JSON: treat as single string or comma-separated
              $selectedSnacks = array_map('trim', explode(',', $raw));
              $selectedSnacks = array_filter($selectedSnacks, fn($v) => $v !== '');
            }
          } elseif (is_array($raw)) {
            $first = reset($raw);
            if (is_string($first)) {
              $selectedSnacks = array_filter($raw, fn($v) => is_string($v) && $v !== '');
            } elseif (is_array($first) && isset($first['nama'])) {
              $selectedSnacks = array_column($raw, 'nama');
            }
          }
          $snackOptions = [
            'Kue Basah (per porsi)',
            'Pisang Goreng (per porsi)',
            'Roti Bakar',
            'Kue Kering'
          ];
        @endphp
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          @foreach($snackOptions as $option)
            <label style="display:flex;align-items:center;gap:8px;padding:12px;border:2px solid #e5e7eb;border-radius:8px;cursor:pointer;background:#f8f9fa">
              <input type="checkbox" 
                     name="kebutuhan_snack[]" 
                     value="{{ $option }}"
                     {{ in_array($option, $selectedSnacks) ? 'checked' : '' }}
                     style="width:18px;height:18px">
              <span style="font-weight:500">{{ $option }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <div style="margin-bottom:16px">
        <label style="display:block;margin-bottom:8px;font-weight:600">Kebutuhan Makan</label>
        @php
          $selectedMeals = [];
          $raw = $p->kebutuhan_makan ?? '';
          if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
              $first = reset($decoded);
              if (is_string($first)) {
                $selectedMeals = array_filter($decoded, fn($v) => is_string($v) && $v !== '');
              } elseif (is_array($first) && isset($first['nama'])) {
                $selectedMeals = array_column($decoded, 'nama');
              }
            } else {
              $selectedMeals = array_map('trim', explode(',', $raw));
              $selectedMeals = array_filter($selectedMeals, fn($v) => $v !== '');
            }
          } elseif (is_array($raw)) {
            $first = reset($raw);
            if (is_string($first)) {
              $selectedMeals = array_filter($raw, fn($v) => is_string($v) && $v !== '');
            } elseif (is_array($first) && isset($first['nama'])) {
              $selectedMeals = array_column($raw, 'nama');
            }
          }
          $makanOptions = [
            'Nasi Box Premium',
            'Prasmanan (per orang)',
            'Nasi Box Standar',
            'Paket Makan Siang'
          ];
        @endphp
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          @foreach($makanOptions as $option)
            <label style="display:flex;align-items:center;gap:8px;padding:12px;border:2px solid #e5e7eb;border-radius:8px;cursor:pointer;background:#f8f9fa">
              <input type="checkbox" 
                     name="kebutuhan_makan[]" 
                     value="{{ $option }}"
                     {{ in_array($option, $selectedMeals) ? 'checked' : '' }}
                     style="width:18px;height:18px">
              <span style="font-weight:500">{{ $option }}</span>
            </label>
          @endforeach
        </div>
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


@endsection
<script>
document.getElementById('editForm').addEventListener('submit', function(e) {
  // Convert checkbox arrays to JSON strings
  const snackCheckboxes = document.querySelectorAll('input[name="kebutuhan_snack[]"]');
  const snackSelected = Array.from(snackCheckboxes)
    .filter(cb => cb.checked)
    .map(cb => cb.value);
  
  const mealCheckboxes = document.querySelectorAll('input[name="kebutuhan_makan[]"]');
  const mealsSelected = Array.from(mealCheckboxes)
    .filter(cb => cb.checked)
    .map(cb => cb.value);
  
  // Create hidden inputs with JSON values
  let snackInput = document.querySelector('input[name="kebutuhan_snack"]');
  if (!snackInput) {
    snackInput = document.createElement('input');
    snackInput.type = 'hidden';
    snackInput.name = 'kebutuhan_snack';
    this.appendChild(snackInput);
  }
  snackInput.value = JSON.stringify(snackSelected);
  
  let mealInput = document.querySelector('input[name="kebutuhan_makan"]');
  if (!mealInput) {
    mealInput = document.createElement('input');
    mealInput.type = 'hidden';
    mealInput.name = 'kebutuhan_makan';
    this.appendChild(mealInput);
  }
  mealInput.value = JSON.stringify(mealsSelected);
  
  // Remove the checkbox inputs before submit to avoid duplicate submission
  snackCheckboxes.forEach(cb => cb.name = '');
  mealCheckboxes.forEach(cb => cb.name = '');
});
</script>
