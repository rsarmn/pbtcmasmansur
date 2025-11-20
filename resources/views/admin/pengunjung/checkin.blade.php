@extends('layout')
@section('content')
<style>
  .form-shell{background:#fff;border-radius:12px;padding:24px;max-width:600px;margin:20px auto;box-shadow:0 2px 8px rgba(0,0,0,0.1)}
  .form-group{margin-bottom:16px}
  .form-label{display:block;margin-bottom:6px;font-weight:600;color:#333}
  .form-input{width:100%;padding:10px 12px;border:1px solid #ddd;border-radius:6px;font-size:14px}
  .btn-primary{background:var(--brand);color:#fff;border:0;padding:12px 24px;border-radius:6px;font-weight:600;cursor:pointer}
  .btn-primary:hover{filter:brightness(110%)}
</style>

<div class="form-shell">
  <h2 style="margin:0 0 8px 0;color:var(--brand)">Check-In: {{ $p->nama }}</h2>
  <p style="color:#666;margin-bottom:24px">Booking ID: #{{ $p->id }} | Check-in: {{ $p->check_in }}</p>

  @if($errors->any())
    <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:6px;margin-bottom:16px">
      @foreach($errors->all() as $error)
        <div>• {{ $error }}</div>
      @endforeach
    </div>
  @endif

  <form action="{{ route('pengunjung.checkin.process', $p->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="form-group">
      <label class="form-label">Nama Tamu</label>
      <input type="text" value="{{ $p->nama }}" class="form-input" readonly>
    </div>

    <div class="form-group">
      <label class="form-label">Jenis Tamu</label>
      <input type="text" value="{{ $p->jenis_tamu }}" class="form-input" readonly>
    </div>

    @if($p->jenis_tamu == 'Corporate')
      <div class="form-group">
        <label class="form-label">Nama Kegiatan</label>
        <input type="text" value="{{ $p->nama_kegiatan }}" class="form-input" readonly>
      </div>
      <div class="form-group">
        <label class="form-label">PIC</label>
        <input type="text" value="{{ $p->nama_pic }} ({{ $p->no_telp_pic }})" class="form-input" readonly>
      </div>
      <div class="form-group">
        <label class="form-label">Jumlah Peserta</label>
        <input type="text" value="{{ $p->jumlah_peserta }}" class="form-input" readonly>
      </div>
    @endif

    <div class="form-group">
      <label class="form-label">Tipe Identitas <span style="color:red">*</span></label>
      <select name="identity_type" class="form-input" required>
        <option value="">-- Pilih Tipe Identitas --</option>
        <option value="KTP" {{ ($p->identity_type ?? '') == 'KTP' ? 'selected' : '' }}>KTP</option>
        <option value="KTM" {{ ($p->identity_type ?? '') == 'KTM' ? 'selected' : '' }}>KTM</option>
        <option value="SIM" {{ ($p->identity_type ?? '') == 'SIM' ? 'selected' : '' }}>SIM</option>
      </select>
      <small style="color:#666;display:block;margin-top:4px">Tamu wajib menyerahkan identitas asli (KTP/KTM/SIM)</small>
    </div>

    <div class="form-group">
      <label class="form-label">Upload Foto Identitas <span style="color:red">*</span></label>
      <input type="file" name="bukti_identitas" class="form-input" accept=".jpg,.jpeg,.png,.pdf" required>
      <small style="color:#666;display:block;margin-top:4px">Format: JPG, PNG, PDF (max 5MB)</small>
      @if($p->bukti_identitas)
        <div style="margin-top:8px">
          <a href="{{ Storage::url(str_replace('public/','',$p->bukti_identitas)) }}" target="_blank" style="color:#2563eb">Lihat identitas yang sudah diupload</a>
        </div>
      @endif
    </div>

    <div style="background:#fef3c7;padding:12px;border-radius:6px;margin-bottom:16px">
      <strong>⚠️ Penting:</strong> Identitas asli akan disimpan oleh admin dan dikembalikan saat check-out.
    </div>

    <div style="display:flex;gap:12px">
      <button type="submit" class="btn-primary">✓ Proses Check-In</button>
      <a href="{{ route('pengunjung.show', $p->id) }}" class="btn-primary" style="background:#6b7280;text-decoration:none;display:inline-block">Batal</a>
    </div>
  </form>
</div>

@endsection
