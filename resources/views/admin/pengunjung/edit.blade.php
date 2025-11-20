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
            @if($isSelected) ✓ @endif
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

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
        <div>
          <label style="display:block;margin-bottom:4px;font-weight:600">Kebutuhan Snack</label>
          <input type="text" name="kebutuhan_snack" value="{{ old('kebutuhan_snack', $p->kebutuhan_snack) }}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
        </div>
        <div>
          <label style="display:block;margin-bottom:4px;font-weight:600">Kebutuhan Makan</label>
          <input type="text" name="kebutuhan_makan" value="{{ old('kebutuhan_makan', $p->kebutuhan_makan) }}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px">
        </div>
      </div>
    </div>

    <div style="display:flex;gap:12px;margin-top:24px">
      <button type="submit" class="pill-btn" style="background:#2563eb;color:#fff;padding:12px 32px;border:none;cursor:pointer">
        💾 Simpan Perubahan
      </button>
      <a href="{{ route('pengunjung.show', $p->id) }}" class="pill-btn" style="background:#6b7280;color:#fff;padding:12px 32px">
        ❌ Batal
      </a>
    </div>
  </form>
</div>
@endsection
