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
        <input type="text" name="no_telp_pic" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control" placeholder="Nomor WhatsApp Terdaftar">
      </div>
      <div class="mb-3">
        <label>No Identitas (KTP/Paspor)</label>
        <input type="text" name="no_identitas" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control" placeholder="Tulis  No Identitas Terdaftar">
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

      <!-- selected room is already present in the multi-select below, no hidden input needed -->
      <div class="mb-3">
        <label>Pilih Kamar Tambahan</label>
        <select name="kode_kamar[]" multiple class="form-control" size="6">
          @foreach($kamars as $k)
            <option value="{{ $k->kode_kamar }}" {{ $k->kode_kamar == $selectedRoom->kode_kamar ? 'selected' : '' }}>
              {{ $k->kode_kamar }} - {{ $k->jenis_kamar }} ({{ number_format($k->harga,0,',','.') }}/malam)
            </option>
          @endforeach
        </select>
        <small class="text-muted">Tekan Ctrl (Cmd di Mac) untuk pilih beberapa kamar</small>
      </div>

      <div class="row">
        <div class="col-md-12 mb-3">
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

    // Form validation dengan SweetAlert
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const namaPic = document.querySelector('[name="nama_pic"]').value.trim();
            const namaKegiatan = document.querySelector('[name="nama_kegiatan"]').value.trim();
            const checkInVal = checkIn.value;
            const checkOutVal = checkOut.value;

            if (!namaPic || !namaKegiatan || !checkInVal || !checkOutVal) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: 'Mohon lengkapi semua field yang wajib diisi (Nama PIC, Nama Kegiatan, Check-in, Check-out)',
                    confirmButtonColor: '#a0203c'
                });
                return false;
            }
        });
    }
});
</script>

@endsection
