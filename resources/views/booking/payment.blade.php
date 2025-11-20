@extends('layouts.app')

@section('content')
<style>
  :root{
    --peach:#f1cfc4;
    --primary:#a0203c;
    --light:#fbf7f6;
    --white:#fff;
  }
  body{background:var(--light);}
  .header-title{text-align:center;font-weight:800;font-size:42px;margin-bottom:10px;color:#111;}

  /* ===== Stepper ===== */
  .stepper-wrapper{
    border:3px solid var(--primary);
    border-radius:20px;
    padding:14px 18px;
    margin-bottom:25px;
    background:#fff;
  }
  .stepper{
    display:flex;align-items:center;gap:18px;justify-content:space-between;
  }
  .step-item{display:flex;align-items:center;gap:10px}
  .dot{width:16px;height:16px;border-radius:999px;background:var(--primary)}
  .step-label{font-weight:700;color:#111}

  .bar{height:8px;flex:1;min-width:180px;background:var(--primary);border-radius:999px;}

  /* ===== Alert ===== */
  .alert-box{background:var(--peach);padding:14px 18px;border-radius:14px;margin-bottom:20px;display:flex;align-items:center;gap:12px}
  .alert-box i{background:var(--primary);color:#fff;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-weight:bold}
  .alert-text strong{display:block;color:#111;font-size:1.1rem}
  .alert-text span{font-size:.9rem;color:#333}

  /* ===== Card ===== */
  .card-section{
    border:2px solid rgba(160,32,60,.4);
    border-radius:16px;
    background:#fff;
    padding:20px;
  }

  .section-title{font-weight:800;color:#111;margin-bottom:4px;}
  .section-value{margin-bottom:10px;}

  /* ===== Payment area ===== */
  .payment-wrap{display:flex;gap:24px;flex-wrap:wrap;}
  .payment-logos{
    flex:1 1 420px;display:flex;flex-wrap:wrap;gap:14px;
    justify-content:center;align-items:center;
  }
  .payment-logos img{
    width:88px;height:88px;object-fit:contain;background:#fff;
    padding:8px;border-radius:12px;
    box-shadow:0 0 0 1px rgba(0,0,0,.1);
  }
  .qr-box img{width:160px;height:160px;border-radius:10px;}

  .btn-maroon{
    background:var(--primary);color:#fff;border:none;
    border-radius:12px;padding:.7rem 1.2rem;font-weight:700;
  }
  .upload-box input[type="file"]{
    border:2px solid rgba(160,32,60,.4);
    border-radius:12px;padding:.6rem .9rem;width:100%;
  }
</style>

<div class="container py-4">

  <h1 class="header-title">PAYMENTS DETAILS</h1>

  {{-- STEP --}}
  <div class="stepper-wrapper">
    <div class="stepper">
      <div class="step-item"><div class="dot"></div><div class="step-label">Isi Data</div></div>
      <div class="bar"></div>
      <div class="step-item"><div class="dot"></div><div class="step-label">Booking</div></div>
      <div class="bar"></div>
      <div class="step-item"><div class="dot"></div><div class="step-label">Payment</div></div>
    </div>
  </div>

  {{-- ALERT --}}
  <div class="alert-box">
    <i>✓</i>
    <div class="alert-text">
      <strong>Pesanan Anda Terkonfirmasi</strong>
      <span>Segera lakukan pembayaran sebelum 24 jam.</span>
    </div>
  </div>

  <div class="row g-4">

    {{-- LEFT --}}
    <div class="col-lg-7">
      <div class="card-section h-100">

        <h5>Booking Details</h5>

        <div class="row">
          <div class="col-md-6">

            <div class="section-title">NAMA</div>
            <div class="section-value">{{ $pengunjung->nama ?? $pengunjung->nama_pic ??'-' }}</div>

            <div class="section-title">NO TELEPON</div>
            <div class="section-value">{{ $pengunjung->no_telp ?? $pengunjung->no_telp_pic ?? '-' }}</div>

            <div class="section-title">RESERVATION</div>
            <div class="section-value">{{ $durasi ?? '-' }} hari</div>

          </div>

          <div class="col-md-6">

            <div class="section-title">CHECK-IN</div>
            <div class="section-value">
              {{ \Carbon\Carbon::parse($pengunjung->check_in)->translatedFormat('l, d F Y') }} <br><b>12.00 WIB</b>
            </div>

            <div class="section-title">CHECK-OUT</div>
            <div class="section-value">
              {{ \Carbon\Carbon::parse($pengunjung->check_out)->translatedFormat('l, d F Y') }} <br><b>12.00 WIB</b>
            </div>

          </div>
        </div>

        <hr>

        <h5 class="mb-3">Method Payments</h5>

        <div class="payment-wrap">
          <div class="payment-logos">
            <img src="{{ asset('images/BSI.png') }}" alt="BSI">
            <img src="{{ asset('images/BRIMO.png') }}" alt="BRIMO">
            <img src="{{ asset('images/BISA.png') }}" alt="BISA">
            <img src="{{ asset('images/LIVIN.png') }}" alt="LIVIN">
            <img src="{{ asset('images/BCA.png') }}" alt="BCA">
            <img src="{{ asset('images/BNI.png') }}" alt="BNI">
            <img src="{{ asset('images/CIMB.png') }}" alt="CIMB">
          </div>

          <div class="qr-box text-start">
            <img src="{{ asset('images/QRCODE.png') }}" alt="QR CODE">
            <p class="mt-2 fw-bold text-danger">More details for Payment</p>
          </div>
        </div>

        <form class="upload-box mt-4" action="{{ route('booking.payment.upload', $pengunjung->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          <label class="fw-bold mb-1">Upload Bukti Pembayaran</label>
          <input type="file" name="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf" required>
          <small class="text-muted">Format yang diperbolehkan: JPG, PNG, atau PDF.</small>

          <div class="mt-3">
            <button type="submit" class="btn-maroon">Kirim Bukti</button>
          </div>
        </form>

      </div>
    </div>

    {{-- RIGHT --}}
    <div class="col-lg-5">
      <div class="card-section h-100">

        <h5>Booking Summary</h5>

        <div class="border rounded p-3 mb-3">

          <div class="row">
            <div class="col-6">
              <div class="fw-bold">CHECK-IN</div>
              <div>{{ \Carbon\Carbon::parse($pengunjung->check_in)->translatedFormat('l, d F Y') }}<br><b>12.00 WIB</b></div>
            </div>
            <div class="col-6">
              <div class="fw-bold">CHECK-OUT</div>
              <div>{{ \Carbon\Carbon::parse($pengunjung->check_out)->translatedFormat('l, d F Y') }}<br><b>12.00 WIB</b></div>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-6">
              <div class="fw-bold">ROOM SELECTED</div>
              <div>{{ $kamar->jenis_kamar ?? '-' }}</div>
            </div>
            <div class="col-6">
              <div class="fw-bold">RESERVATION</div>
              <div>{{ $durasi ?? '-' }} hari</div>
            </div>
          </div>

        </div>

        <h5>Price Summary</h5>

        {{-- Harga Kamar --}}
        <div class="d-flex justify-content-between">
          <span>Hunian ({{ $kamar->jenis_kamar }})</span>
          <span>Rp{{ number_format($kamar->harga,0,',','.') }}</span>
        </div>

        <small class="text-muted d-block mb-2">
          1 kamar × {{ $durasi }} hari × Rp{{ number_format($kamar->harga,0,',','.') }}
          = <strong>Rp{{ number_format($totalKamar,0,',','.') }}</strong>
        </small>

        {{-- Rincian Snack / Makan --}}
        @if(!empty($detailMenus))
          <h6 class="mt-3 fw-bold">Rincian Konsumsi</h6>

          @foreach($detailMenus as $dm)
          <div class="d-flex justify-content-between">
            <span>{{ $dm['nama'] }} ({{ $dm['porsi'] }} porsi)</span>
            <span>Rp{{ number_format($dm['total'],0,',','.') }}</span>
          </div>
          @endforeach

          <div class="d-flex justify-content-between mt-2 fw-bold">
            <span>Total Konsumsi</span>
            <span>Rp{{ number_format($totalMenu,0,',','.') }}</span>
          </div>

        @endif

        <hr>

        <div class="d-flex justify-content-between fw-bold">
          <span>Total</span>
          <span>Rp{{ number_format($totalPembayaran,0,',','.') }}</span>
        </div>

        <div class="mt-3">
          <a href="{{ url('/') }}" class="btn-maroon w-100 text-center" style="display:block; border-radius:12px; background:#a0203c; color:white; padding:.7rem 1rem; font-weight:700;">
              Kembali ke Halaman Utama
          </a>
      </div>

      </div>
    </div>

  </div>

</div>
@endsection
