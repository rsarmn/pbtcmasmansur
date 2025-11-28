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
      {{-- expose room prices to JS so frontend can sum selected room costs on submit --}}
      <script>
        window.roomPrices = {!! json_encode($kamars->pluck('harga','kode_kamar')) !!};
      </script>
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
        <label style="display:block;margin-bottom:4px;font-weight:600">Kebutuhan Snack</label>
        @php
          // existing snack data may be JSON array of strings or objects
          $existingSnacksRaw = $p->kebutuhan_snack ?? '[]';
          $existingSnacks = [];
          try {
            $decoded = is_string($existingSnacksRaw) ? json_decode($existingSnacksRaw, true) : $existingSnacksRaw;
            if (is_array($decoded)) {
              // normalize to objects with nama,porsi,harga
              foreach ($decoded as $it) {
                if (is_string($it)) {
                  $existingSnacks[] = ['nama' => $it, 'porsi' => 1, 'harga' => 0];
                } elseif (is_array($it)) {
                  $existingSnacks[] = [
                    'nama' => $it['nama'] ?? ($it[0] ?? ''),
                    'porsi' => isset($it['porsi']) ? (int)$it['porsi'] : (isset($it[1]) ? (int)$it[1] : 1),
                    'harga' => isset($it['harga']) ? (float)$it['harga'] : 0,
                  ];
                }
              }
            }
          } catch (\Exception $e){ $existingSnacks = []; }
        @endphp

        <div style="border:1px solid #e5e7eb;border-radius:8px;padding:8px">
          @foreach($snackMenus as $menu)
            @php
              $found = null;
              foreach($existingSnacks as $es){ if(trim($es['nama']) == trim($menu->nama_menu)){ $found = $es; break; } }
            @endphp
            <div style="display:flex;align-items:center;gap:12px;padding:8px;border-bottom:1px solid #f3f4f6">
              <div style="flex:1">
                <label style="font-weight:600">{{ $menu->nama_menu }}</label>
                <div style="font-size:13px;color:#666">{{ $menu->deskripsi ?? '' }}</div>
                <div style="font-size:13px;color:#10b981;font-weight:700">Rp {{ number_format($menu->harga,0,',','.') }}</div>
              </div>
              <div style="display:flex;align-items:center;gap:8px">
                <input type="checkbox" data-name="{{ $menu->nama_menu }}" class="menu-snack-cb" id="snack_{{ $menu->id }}" {{ $found ? 'checked' : '' }}>
                <input type="number" min="1" value="{{ $found['porsi'] ?? 1 }}" class="menu-snack-qty" data-price="{{ $menu->harga }}" style="width:86px;padding:8px;border:1px solid #ddd;border-radius:6px" {{ $found ? '' : 'disabled' }}>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div style="margin-bottom:16px">
        <label style="display:block;margin-bottom:8px;font-weight:600">Kebutuhan Makan</label>
        @php
          $existingMealsRaw = $p->kebutuhan_makan ?? '[]';
          $existingMeals = [];
          try { $dec = is_string($existingMealsRaw) ? json_decode($existingMealsRaw, true) : $existingMealsRaw; if(is_array($dec)){ foreach($dec as $it){ if(is_string($it)) $existingMeals[]=['nama'=>$it,'porsi'=>1,'harga'=>0]; elseif(is_array($it)) $existingMeals[]=['nama'=>$it['nama']??'','porsi'=>isset($it['porsi'])?(int)$it['porsi']:(isset($it[1])?(int)$it[1]:1),'harga'=>isset($it['harga'])?(float)$it['harga']:0]; } } } catch(\Exception $e){ $existingMeals = []; }
        @endphp
        <div style="border:1px solid #e5e7eb;border-radius:8px;padding:8px">
          @foreach($mealMenus as $menu)
            @php
              $found = null;
              foreach($existingMeals as $em){ if(trim($em['nama']) == trim($menu->nama_menu)){ $found = $em; break; } }
            @endphp
            <div style="display:flex;align-items:center;gap:12px;padding:8px;border-bottom:1px solid #f3f4f6">
              <div style="flex:1">
                <label style="font-weight:600">{{ $menu->nama_menu }}</label>
                <div style="font-size:13px;color:#666">{{ $menu->deskripsi ?? '' }}</div>
                <div style="font-size:13px;color:#10b981;font-weight:700">Rp {{ number_format($menu->harga,0,',','.') }}</div>
              </div>
              <div style="display:flex;align-items:center;gap:8px">
                <input type="checkbox" data-name="{{ $menu->nama_menu }}" class="menu-meal-cb" id="meal_{{ $menu->id }}" {{ $found ? 'checked' : '' }}>
                <input type="number" min="1" value="{{ $found['porsi'] ?? 1 }}" class="menu-meal-qty" data-price="{{ $menu->harga }}" style="width:86px;padding:8px;border:1px solid #ddd;border-radius:6px" {{ $found ? '' : 'disabled' }}>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <input type="hidden" name="kebutuhan_snack" id="kebutuhan_snack_input">
    <input type="hidden" name="kebutuhan_makan" id="kebutuhan_makan_input">
    <input type="hidden" name="total_harga" id="total_harga_input" value="{{ $p->total_harga ?? 0 }}">

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
// Enable/disable qty inputs based on checkbox and collect structured data on submit
document.addEventListener('DOMContentLoaded', function(){
  function toggleInput(cb, qtySelector){
    const qty = cb.parentElement.querySelector(qtySelector);
    if(!qty) return;
    qty.disabled = !cb.checked;
  }

  document.querySelectorAll('.menu-snack-cb').forEach(cb => {
    toggleInput(cb, '.menu-snack-qty');
    cb.addEventListener('change', function(){ toggleInput(this, '.menu-snack-qty'); });
  });
  document.querySelectorAll('.menu-meal-cb').forEach(cb => {
    toggleInput(cb, '.menu-meal-qty');
    cb.addEventListener('change', function(){ toggleInput(this, '.menu-meal-qty'); });
  });

  document.getElementById('editForm').addEventListener('submit', function(e){
    // build snack array
    const snacks = [];
    document.querySelectorAll('.menu-snack-cb').forEach(cb => {
      if(cb.checked){
        const name = cb.getAttribute('data-name');
        const qty = parseInt(cb.parentElement.querySelector('.menu-snack-qty').value || 0);
        const price = parseFloat(cb.parentElement.querySelector('.menu-snack-qty').getAttribute('data-price') || 0);
        snacks.push({nama: name, porsi: qty, harga: price});
      }
    });

    const meals = [];
    document.querySelectorAll('.menu-meal-cb').forEach(cb => {
      if(cb.checked){
        const name = cb.getAttribute('data-name');
        const qty = parseInt(cb.parentElement.querySelector('.menu-meal-qty').value || 0);
        const price = parseFloat(cb.parentElement.querySelector('.menu-meal-qty').getAttribute('data-price') || 0);
        meals.push({nama: name, porsi: qty, harga: price});
      }
    });

    // compute room total from selected kode_kamar[] using roomPrices exposed above
    let roomTotal = 0;
    const kodeSelect = document.querySelector('select[name="kode_kamar[]"]');
    if(kodeSelect){
      Array.from(kodeSelect.selectedOptions).forEach(opt => {
        const code = opt.value;
        const price = parseFloat(window.roomPrices[code] || 0);
        if(!isNaN(price)) roomTotal += price;
      });
    }

    // compute total harga (rooms + snacks + meals)
    let total = roomTotal;
    snacks.forEach(i=> total += (i.porsi || 0) * (i.harga || 0));
    meals.forEach(i=> total += (i.porsi || 0) * (i.harga || 0));

    document.getElementById('kebutuhan_snack_input').value = JSON.stringify(snacks);
    document.getElementById('kebutuhan_makan_input').value = JSON.stringify(meals);
    document.getElementById('total_harga_input').value = total;

    // allow original submit to continue
  });
});
</script>
