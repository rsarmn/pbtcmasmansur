@extends('layout')
@section('content')
<style>
  .booking-form{max-width:700px;margin:40px auto;background:#fff;padding:32px;border-radius:16px;box-shadow:0 4px 12px rgba(0,0,0,0.1)}
  .form-group{margin-bottom:20px}
  .form-label{display:block;margin-bottom:8px;font-weight:600;color:#333}
  .form-input,.form-select{width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;font-size:14px}
  .form-input:focus,.form-select:focus{outline:none;border-color:var(--brand)}
  .btn-submit{background:var(--brand);color:#fff;border:0;padding:14px 32px;border-radius:8px;font-weight:700;cursor:pointer;width:100%;font-size:16px}
  .btn-submit:hover{filter:brightness(110%)}
  .room-card{border:2px solid #e5e7eb;padding:16px;border-radius:12px;margin-bottom:12px;cursor:pointer;transition:all 0.3s}
  .room-card:hover{border-color:var(--brand);background:#fef2f2}
  .room-card.selected{border-color:var(--brand);background:#fef2f2;box-shadow:0 0 0 3px rgba(179,18,59,0.1)}
  .room-price{color:var(--brand);font-weight:700;font-size:18px}
</style>

<div class="booking-form">
  <h2 style="color:var(--brand);margin-bottom:8px">Booking Kamar</h2>
  <p style="color:#666;margin-bottom:24px">Isi formulir di bawah untuk booking kamar penginapan</p>

  @if(session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:12px;border-radius:8px;margin-bottom:20px">
      {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:20px">
      @foreach($errors->all() as $error)
        <div>• {{ $error }}</div>
      @endforeach
    </div>
  @endif

  <form action="{{ route('booking.user.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Jenis Tamu -->
    <div class="form-group">
      <label class="form-label">Jenis Tamu <span style="color:red">*</span></label>
      <select name="jenis_tamu" class="form-select" required onchange="toggleTamuType(this.value)">
        <option value="">-- Pilih --</option>
        <option value="Individu">Individu</option>
        <option value="Corporate">Corporate</option>
      </select>
    </div>

    <!-- Info Individu -->
    <div id="individu-section" style="display:none">
      <div class="form-group">
        <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
        <input type="text" name="nama" class="form-input" value="{{ old('nama') }}">
      </div>
      <div class="form-group">
        <label class="form-label">No. Identitas (KTP/KTM/SIM) <span style="color:red">*</span></label>
        <input type="text" name="no_identitas" class="form-input" value="{{ old('no_identitas') }}">
      </div>
      <div class="form-group">
        <label class="form-label">Tipe Identitas <span style="color:red">*</span></label>
        <select name="identity_type" class="form-select">
          <option value="KTP">KTP</option>
          <option value="KTM">KTM</option>
          <option value="SIM">SIM</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">No. Telepon <span style="color:red">*</span></label>
        <input type="text" name="no_telp" class="form-input" value="{{ old('no_telp') }}">
      </div>
    </div>

    <!-- Info Corporate -->
    <div id="corporate-section" style="display:none">
      <div class="form-group">
        <label class="form-label">Nama Organisasi/Perusahaan <span style="color:red">*</span></label>
        <input type="text" name="nama" class="form-input" value="{{ old('nama') }}">
      </div>
      <div class="form-group">
        <label class="form-label">Nama Kegiatan <span style="color:red">*</span></label>
        <input type="text" name="nama_kegiatan" class="form-input" value="{{ old('nama_kegiatan') }}">
      </div>
      <div class="form-group">
        <label class="form-label">Nama PIC (Penanggung Jawab) <span style="color:red">*</span></label>
        <input type="text" name="nama_pic" class="form-input" value="{{ old('nama_pic') }}">
      </div>
      <div class="form-group">
        <label class="form-label">No. HP PIC <span style="color:red">*</span></label>
        <input type="text" name="no_telp_pic" class="form-input" value="{{ old('no_telp_pic') }}">
      </div>
      <div class="form-group">
        <label class="form-label">Asal Persyarikatan <span style="color:red">*</span></label>
        <input type="text" name="asal_persyarikatan" class="form-input" value="{{ old('asal_persyarikatan') }}">
      </div>
      <div class="form-group">
        <label class="form-label">Tanggal Persyarikatan</label>
        <input type="date" name="tanggal_persyarikatan" class="form-input" value="{{ old('tanggal_persyarikatan') }}">
      </div>
      <div class="form-group">
        <label class="form-label">Jumlah Peserta <span style="color:red">*</span></label>
        <input type="number" name="jumlah_peserta" class="form-input" value="{{ old('jumlah_peserta') }}" min="1">
      </div>
      <div class="form-group">
        <label class="form-label">Jumlah Kamar Dibutuhkan <span style="color:red">*</span></label>
        <input type="number" name="jumlah_kamar" class="form-input" value="{{ old('jumlah_kamar') }}" min="1">
      </div>
      <div class="form-group">
        <label class="form-label">Kebutuhan Snack (porsi)</label>
        <input type="number" name="kebutuhan_snack" class="form-input" value="{{ old('kebutuhan_snack') }}" min="0">
      </div>
      <div class="form-group">
        <label class="form-label">Kebutuhan Makan (porsi)</label>
        <input type="number" name="kebutuhan_makan" class="form-input" value="{{ old('kebutuhan_makan') }}" min="0">
      </div>
    </div>

    <!-- Tanggal Check-in/out -->
    <div class="form-group">
      <label class="form-label">Tanggal Check-in <span style="color:red">*</span></label>
      <input type="date" name="check_in" class="form-input" required value="{{ old('check_in') }}">
    </div>
    <div class="form-group">
      <label class="form-label">Tanggal Check-out <span style="color:red">*</span></label>
      <input type="date" name="check_out" class="form-input" required value="{{ old('check_out') }}">
    </div>

    <!-- Pilih Jenis Kamar -->
    <div class="form-group">
      <label class="form-label">Pilih Jenis Kamar <span style="color:red">*</span></label>
      <div id="room-options">
        @foreach($jenisKamarList as $jenis)
        <div class="room-card" onclick="selectRoom('{{ $jenis->jenis_kamar }}', {{ $jenis->harga }})">
          <input type="radio" name="jenis_kamar_pilihan" value="{{ $jenis->jenis_kamar }}" style="display:none" required>
          <div style="display:flex;justify-content:space-between;align-items:center">
            <div>
              <div style="font-weight:700;font-size:16px;margin-bottom:4px">{{ $jenis->jenis_kamar }}</div>
              <div style="font-size:13px;color:#666">{{ $jenis->gedung }} • {{ $jenis->fasilitas }}</div>
              <div style="font-size:12px;color:#999;margin-top:4px">Tersedia: {{ $jenis->available_count }} kamar</div>
            </div>
            <div class="room-price">Rp {{ number_format($jenis->harga, 0, ',', '.') }}</div>
          </div>
        </div>
        @endforeach
      </div>
      <small style="color:#666">*Harga per malam, tidak termasuk makan</small>
    </div>

    <!-- Special Request -->
    <div class="form-group">
      <label class="form-label">Permintaan Khusus</label>
      <textarea name="special_request" class="form-input" rows="3">{{ old('special_request') }}</textarea>
    </div>

    <!-- Upload Bukti Pembayaran -->
    <div class="form-group">
      <label class="form-label">Upload Bukti Pembayaran <span style="color:red">*</span></label>
      <input type="file" name="bukti_pembayaran" class="form-input" accept=".jpg,.jpeg,.png,.pdf" required>
      <small style="color:#666;display:block;margin-top:8px">Format: JPG, PNG, PDF (max 5MB)</small>
      <div style="background:#fef3c7;padding:12px;border-radius:6px;margin-top:12px;font-size:13px">
        <strong>Info Pembayaran:</strong><br>
        Transfer ke: Bank BCA 1234567890 a.n. Pesantren Mahasiswa<br>
        Setelah upload, admin akan verifikasi dalam 1x24 jam
      </div>
    </div>

    <button type="submit" class="btn-submit">Kirim Booking</button>
  </form>
</div>

<script>
function toggleTamuType(jenis) {
  const individuSection = document.getElementById('individu-section');
  const corporateSection = document.getElementById('corporate-section');
  
  if(jenis === 'Individu') {
    individuSection.style.display = 'block';
    corporateSection.style.display = 'none';
  } else if(jenis === 'Corporate') {
    individuSection.style.display = 'none';
    corporateSection.style.display = 'block';
  } else {
    individuSection.style.display = 'none';
    corporateSection.style.display = 'none';
  }
}

function selectRoom(jenis, harga) {
  // Remove all selected
  document.querySelectorAll('.room-card').forEach(card => {
    card.classList.remove('selected');
  });
  
  // Select this one
  event.currentTarget.classList.add('selected');
  event.currentTarget.querySelector('input[type="radio"]').checked = true;
}
</script>

@endsection
