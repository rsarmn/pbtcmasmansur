@extends('layouts.app')

@section('content')
<style>
  :root{
    --peach:#f1cfc4;
    --primary:#a0203c;
    --primary-20:#e0b6c0;
    --white:#fff;
    --page:#fbf7f6;
    --muted:#7a7a7a;
    --radius:14px;
  }
  .page-bg{background:var(--page);}

  .stepper-box{
    border:3px solid var(--primary);
    border-radius:18px;
    background:var(--white);
    padding:14px 18px;
    display:flex; align-items:center; gap:22px;
  }

  .step{display:flex; align-items:center; gap:10px; flex:1;}
  .step strong{font-weight:800; color:#111}
  .dot{width:24px;height:24px;border-radius:999px;background:var(--primary);}
  .dot.hollow{background:#fff;border:3px solid var(--primary)}
  .bar{height:12px;border-radius:999px;background:var(--primary-20);flex:1;overflow:hidden;position:relative}
  .bar .fill{position:absolute;inset:0;background:var(--primary);width:100%}
  .step-pending .fill{background:var(--primary-20);width:92%}

  .card-outline{
    background:var(--white);
    border:2px solid rgba(160,32,60,.35);
    border-radius:18px;
    padding:20px;
  }

  .img-room{width:100%; border-radius:14px; object-fit:cover}

  label{font-weight:700; color:#111}
  .form-control,.form-select,textarea{
    border:2px solid rgba(160,32,60,.45)!important;
    border-radius:var(--radius)!important;
    padding:.7rem .9rem;
    background:var(--white); color:#111;
  }

  .btn-primary-maroon{
    background:var(--primary); color:#fff;
    border:none; border-radius:12px;
    padding:.7rem 1.2rem; font-weight:700
  }

  .btn-ghost{
    background:var(--white); color:var(--primary);
    border:2px solid rgba(160,32,60,.35);
    border-radius:12px; padding:.7rem 1.2rem; font-weight:700
  }
</style>

<div class="page-bg">
  <div class="container pt-2 pb-4">

    <!-- STEPPER -->
    <div class="stepper-box mb-3" style="margin-top:-10px;">
      <div class="step step-complete">
        <span class="dot"></span>
        <strong>Isi Data</strong>
        <div class="bar"><span class="fill"></span></div>
      </div>

      <div class="step step-complete">
        <span class="dot"></span>
        <strong>Booking</strong>
        <div class="bar"><span class="fill"></span></div>
      </div>

      <div class="step step-pending">
        <span class="dot hollow"></span>
        <strong>Payment</strong>
        <div class="bar"><span class="fill"></span></div>
      </div>
    </div>

    <!-- FORM -->
    <form action="{{ route('booking.individu.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="row g-4">

        <!-- LEFT -->
        <div class="col-lg-7">
          <div class="card-outline">

            <!-- NAMA -->
            <div class="mb-3">
              <label>Nama Lengkap</label>
              <input 
                  type="text" 
                  name="nama" 
                  class="form-control" 
                  value="{{ old('nama') }}"
                  required
                  placeholder="Tulis nama lengkap">
              @error('nama')
                  <div class="text-danger small">{{ $message }}</div>
              @enderror
            </div>

            <div class="row">
              <!-- NO IDENTITAS -->
              <div class="col-md-6 mb-3">
                <label>No Identitas (KTP/Paspor)</label>
                <input 
                    type="text" 
                    name="no_identitas" 
                    class="form-control"
                    value="{{ old('no_identitas') }}"
                    required
                    placeholder="Nomor identitas">
                @error('no_identitas')
                  <div class="text-danger small">{{ $message }}</div>
                @enderror
              </div>

              <!-- NO TELEPON -->
              <div class="col-md-6 mb-3">
                <label>No Telepon</label>
                <input 
                    type="text" 
                    name="no_telp" 
                    class="form-control"
                    value="{{ old('no_telp') }}"
                    required
                    placeholder="08xxxxxxxxxx">
                @error('no_telp')
                  <div class="text-danger small">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- FILE -->
            <div class="mb-3">
              <label>Upload Bukti Identitas</label>
              <input type="file" name="bukti_identitas" class="form-control" accept=".jpg,.jpeg,.png">
            </div>

            <!-- SPECIAL REQUEST -->
            <div class="mb-3">
              <label>Special Request</label>
              <textarea name="special_request" rows="4" class="form-control" placeholder="Tulis permintaan khusus (opsional)">{{ old('special_request') }}</textarea>
            </div>

            <input type="hidden" name="jenis_tamu" value="individu">

            <div class="d-flex gap-3 mt-2">
              <button type="submit" class="btn-primary-maroon">Book now</button>
              <a href="{{ url()->previous() }}" class="btn-ghost">Cancel</a>
            </div>

          </div>
        </div>

        <!-- RIGHT -->
        <div class="col-lg-5">
          <div class="card-outline h-100">

            @php
              $fotoMap = [
                'Deluxe'            => 'deluxe.jpg',
                'Guestroom AC'      => 'Guestroom AC.jpeg',
                'Standard'          => 'Standar Room.jpg',
                'Student AC'        => 'Student AC.jpeg',
                'Student Non AC'    => 'Student Non AC.jpg',
              ];

              $fotoKamar = $selectedRoom 
                ? ($fotoMap[$selectedRoom->jenis_kamar] ?? 'default.jpg')
                : 'default.jpg';
            @endphp

            <!-- FOTO -->
            <img 
              src="{{ asset('images/' . $fotoKamar) }}"
              class="img-room mb-3"
              alt="{{ $selectedRoom->jenis_kamar }}"
              style="height:230px;"
            >

            <!-- JUDUL -->
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h4 class="fw-bold m-0">{{ $selectedRoom->jenis_kamar }}</h4>
              <span class="fw-bold">{{ number_format($selectedRoom->harga,0,',','.') }}/malam</span>
            </div>

            <!-- HIDDEN ID KAMAR -->
            <input type="hidden" name="kode_kamar" value="{{ $selectedRoom->id }}">
            
            <!-- JUMLAH KAMAR -->
            <div class="mb-3">
                <label>Jumlah Kamar</label>
                <input 
                    type="number" 
                    name="jumlah_kamar" 
                    class="form-control"
                    min="1"
                    required
                    placeholder="Jumlah Kamar">
            </div>

            <!-- TANGGAL -->
            <div class="mb-3">
              <label>Check-in</label>
              <input 
                  type="date" 
                  name="check_in" 
                  id="check_in"
                  class="form-control"
                  required
                  value="{{ old('check_in') }}">
              @error('check_in')
                <div class="text-danger small">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label>Check-out</label>
              <input 
                  type="date" 
                  name="check_out"
                  id="check_out"
                  class="form-control"
                  required
                  value="{{ old('check_out') }}">
              @error('check_out')
                <div class="text-danger small">{{ $message }}</div>
              @enderror
            </div>

          </div>
        </div>

      </div>
    </form>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const today = new Date().toISOString().split("T")[0];

    const checkIn = document.getElementById("check_in");
    const checkOut = document.getElementById("check_out");

    checkIn.min = today;

    checkIn.addEventListener("change", function () {
        let inDate = new Date(this.value);
        let minOut = new Date(inDate);
        minOut.setDate(minOut.getDate() + 1);

        let minOutFormatted = minOut.toISOString().split("T")[0];

        checkOut.min = minOutFormatted;

        if (checkOut.value < minOutFormatted) {
            checkOut.value = minOutFormatted;
        }
    });

});
</script>

@endsection
