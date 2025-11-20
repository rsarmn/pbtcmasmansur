@extends('layouts.app')

@section('content')
<style>
  :root{
    --peach:#f1cfc4;
    --primary:#a0203c;
    --primary-20:#e0b6c0;
    --page:#fbf7f6;
    --white:#fff;
    --radius:14px;
  }

  body{ background:var(--page); }

  /* Stepper */
  .stepper-box{ border:3px solid var(--primary); border-radius:18px; background:#fff; padding:14px 18px;
    display:flex; align-items:center; gap:22px; }

  .step{display:flex;align-items:center;gap:10px;flex:1;}
  .dot{width:20px;height:20px;border-radius:999px;background:var(--primary);}
  .dot.hollow{background:#fff;border:3px solid var(--primary);}
  .bar{height:10px;flex:1;background:var(--primary-20);border-radius:999px;position:relative;}
  .bar .fill{position:absolute;inset:0;background:var(--primary);}
  .step-pending .fill{background:var(--primary-20);}

  /* Cards */
  .card-outline{
    background:#fff;
    border:2px solid rgba(160,32,60,.35);
    border-radius:18px;
    padding:18px;
  }

  .img-room{width:100%;height:230px;object-fit:cover;border-radius:14px;}

  label{font-weight:700;}

  .form-control,.form-select{
    border:2px solid rgba(160,32,60,.45)!important;
    border-radius:var(--radius)!important;
    padding:.7rem .9rem;
  }

  /* LIST SNACK & MAKAN */
  .menu-item{
    background:rgba(160,32,60,.25);
    border:2px solid rgba(160,32,60,.35);
    padding:16px;
    border-radius:14px;
    display:flex;
    align-items:center;
    gap:15px;
  }

  .menu-text{
    flex:1;
    text-align:left;
  }

  .title{font-weight:800;}

  .menu-right{
    margin-left:auto;
    display:flex;
    align-items:center;
  }

  .porsi-box{
    background:#fff;
    padding:10px 14px;
    border-radius:12px;
    display:flex;
    align-items:center;
    gap:10px;
  }

  .counter{
    display:flex;
    align-items:center;
    gap:10px;
  }

  .btn-counter{
    background:#eee;
    border:none;
    padding:6px 12px;
    font-size:18px;
    border-radius:8px;
    font-weight:700;
  }

  .menu-item + .menu-item{ margin-top:12px; }

  .btn-primary-maroon{
    background:var(--primary);color:#fff;border:none;
    border-radius:12px;padding:.7rem 1.2rem;font-weight:700;
  }
</style>

<!-- STEPPER -->
<div class="stepper-box mb-4">
  <div class="step step-complete">
    <span class="dot"></span><strong>Isi Data</strong>
    <div class="bar"><span class="fill"></span></div>
  </div>
  <div class="step step-complete">
    <span class="dot"></span><strong>Booking</strong>
    <div class="bar"><span class="fill"></span></div>
  </div>
  <div class="step step-pending">
    <span class="dot hollow"></span><strong>Payment</strong>
    <div class="bar"><span class="fill"></span></div>
  </div>
</div>


<form action="{{ route('booking.corporate.store') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <input type="hidden" name="jenis_tamu" value="corporate">

<div class="row g-4">

  <!-- LEFT -->
  <div class="col-lg-7">
    <div class="card-outline">
      <div class="mb-3">
        <label>Nama PIC</label>
        <input type="text" name="nama_pic" class="form-control" placeholder="Tulis nama lengkap PIC">
      </div>
      <div class="mb-3">
        <label>No Telepon PIC</label>
        <input type="text" name="no_telp_pic" class="form-control" placeholder="Nomor WhatsApp Terdaftar">
      </div>
      <div class="mb-3">
        <label>No Identitas (KTP/Paspor)</label>
        <input type="text" name="no_identitas" class="form-control" placeholder="Tulis  No Identitas Terdaftar">
      </div>
      <div class="mb-3">
        <label>Upload Bukti Identitas</label>
        <input type="file" name="bukti_identitas" class="form-control" accept="image/*" required>
      </div>
      <div class="mb-3">
        <label>Asal Persyarikatan</label>
        <input type="text" name="asal_persyarikatan" class="form-control" placeholder="Isi Nama Persyarikatan">
      </div>
      <div class="mb-3">
        <label>Tanggal Persyarikatan</label>
        <input type="date" name="tanggal_persyarikatan" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Nama Kegiatan</label>
        <input type="text" name="nama_kegiatan" class="form-control" placeholder="Isi Nama Kegiatan">
      </div>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="col-lg-5">
    <div class="card-outline">

      @php
        $fotoMap = [
          'Deluxe' => 'deluxe.jpg',
          'Guestroom AC' => 'Guestroom AC.jpeg',
          'Standard' => 'Standar Room.jpg',
          'Student AC' => 'Student AC.jpeg',
          'Student Non AC' => 'Student Non AC.jpg',
        ];
        $foto = $selectedRoom ? ($fotoMap[$selectedRoom->jenis_kamar] ?? 'default.jpg') : 'default.jpg';
      @endphp

      <img src="{{ asset('images/'.$foto) }}" class="img-room mb-3">

      <div class="d-flex justify-content-between mb-2">
        <h4 class="fw-bold">{{ $selectedRoom->jenis_kamar }}</h4>
        <span class="fw-bold">{{ number_format($selectedRoom->harga,0,',','.') }}/malam</span>
      </div>

      <input type="hidden" name="kode_kamar" value="{{ $selectedRoom->id }}">
      <div class="row">
        <div class="col-md-6 mb-3">
            <label>Jumlah Kamar</label>
            <input 
                type="number" 
                name="jumlah_kamar" 
                class="form-control"
                min="1"
                required
                placeholder="Jumlah Kamar">
        </div>

      <div class="col-md-6 mb-3">
          <label>Jumlah Peserta</label>
          <input 
              type="number" 
              name="jumlah_peserta" 
              class="form-control"
              min="1"
              required
              placeholder="Jumlah Peserta">
        </div>
      </div>

      <div class="mb-3">
        <label>Check-in</label>
        <input type="date" name="check_in" id="check_in" class="form-control" required>
      </div>

      <div class="mb-3">
        <label>Check-out</label>
        <input type="date" name="check_out" id="check_out" class="form-control" required>
      </div>

      <!-- SPECIAL REQUEST -->
      <div class="mb-3">
        <label>Special Request</label>
        <textarea name="special_request" rows="4" class="form-control">{{ old('special_request') }}</textarea>
         <small class="text-muted d-block mt-1">
            Untuk pemesanan makan/snack, tamu wajib menambahkan minimal 1 pesanan snack atau makanan sebagai DP awal konsumsi. Jika ingin memesan lebih dari 1 porsi, mohon ajukan permintaan dan tuliskan detailnya pada kolom Special Request. Sisa pembayaran konsumsi dapat dilakukan di kasir setelah mendapatkan persetujuan dari admin.
         </small>
      </div>
  </div>
</div>

<!-- SNACK -->
<div class="mt-4">
  <h5 class="fw-bold">Kebutuhan Snack</h5>

  @foreach($snacks as $sn)
  <div class="menu-item">

    <!-- CHECKBOX -->
    <input type="checkbox" class="form-check-input"
        name="kebutuhan_snack[{{ $sn->id }}][pilih]"
        value="1"
        onclick="togglePorsi({{ $sn->id }}, 'snack', this)">

    <div class="menu-text">
      <div class="title">{{ $sn->nama_menu }} – Rp{{ number_format($sn->harga) }}</div>
      <small>{{ $sn->deskripsi }}</small>
    </div>
  </div>
  @endforeach
</div>

<!-- MAKAN -->
<div class="mt-4">
  <h5 class="fw-bold">Kebutuhan Makan</h5>

  @foreach($makans as $mk)
  <div class="menu-item">

    <input type="checkbox" class="form-check-input"
        name="kebutuhan_makan[{{ $mk->id }}][pilih]"
        value="1"
        onclick="togglePorsi({{ $mk->id }},'makan', this)">

    <div class="menu-text">
      <div class="title">{{ $mk->nama_menu }} – Rp{{ number_format($mk->harga) }}</div>
      <small>{{ $mk->deskripsi }}</small>
    </div>
  </div>
  @endforeach
</div>

<div class="text-end mt-4">
  <button class="btn-primary-maroon">Book Now</button>
</div>

</form>

<!-- SCRIPT -->
<script>
function togglePorsi(id, tipe, checkbox) {
    let box = document.getElementById(`box_${tipe}_${id}`);
    if (checkbox.checked) box.style.display = "flex";
    else box.style.display = "none";
}

document.addEventListener("DOMContentLoaded", function () {
    const checkIn = document.getElementById("check_in");
    const checkOut = document.getElementById("check_out");
    const today = new Date().toISOString().split("T")[0];

    function setMinCheckout() {
        if (checkIn.value) {
            let minCO = new Date(checkIn.value);
            minCO.setDate(minCO.getDate() + 1);
            checkOut.min = minCO.toISOString().split("T")[0];
        } else {
            let tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            checkOut.min = tomorrow.toISOString().split("T")[0];
        }
    }

    checkIn.min = today;
    setMinCheckout();

    checkIn.addEventListener("change", function () {
        setMinCheckout();
        if (checkOut.value < checkOut.min) {
            checkOut.value = "";
        }
    });
});
</script>

@endsection
