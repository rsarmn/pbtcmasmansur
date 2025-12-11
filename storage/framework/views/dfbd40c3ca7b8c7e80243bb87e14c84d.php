<?php $__env->startSection('content'); ?>
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

  .btn-maroon{
    background:var(--primary);color:#fff;border:none;
    border-radius:12px;padding:.7rem 1.2rem;font-weight:700;
  }
  .upload-box input[type="file"]{
    border:2px solid rgba(160,32,60,.4);
    border-radius:12px;padding:.6rem .9rem;width:100%;
  }

  /* ===== Accordion Payment ===== */
  .accordion-item{
      margin-bottom:14px;
      border-bottom:1px solid #ccc;
      padding-bottom:10px;
  }
  .accordion-btn{
      width:100%;
      text-align:left;
      background:none;
      border:none;
      font-weight:700;
      font-size:18px;
      color:#be0e4f;
      cursor:pointer;
      padding:8px 0;
  }
  .accordion-btn:focus{outline:none;}

  .accordion-content{
      display:none;
      color:#333;
      font-size:16px;
      margin-top:10px;
      padding-left:10px;
  }

  .accordion-content.show{
      display:block;
  }

  /* === Hide upload section by default === */
  #uploadSection { display: none; }
  #cashButton { display:none; }
</style>

<div class="container py-4">

  <h1 class="header-title">PAYMENTS DETAILS</h1>

  
  <div class="stepper-wrapper">
    <div class="stepper">
      <div class="step-item"><div class="dot"></div><div class="step-label">Isi Data</div></div>
      <div class="bar"></div>
      <div class="step-item"><div class="dot"></div><div class="step-label">Booking</div></div>
      <div class="bar"></div>
      <div class="step-item"><div class="dot"></div><div class="step-label">Payment</div></div>
    </div>
  </div>

  
  <div class="alert-box">
    <i>✓</i>
    <div class="alert-text">
      <strong>Pesanan Anda Terkonfirmasi</strong>
      <span>Segera lakukan pembayaran sebelum 24 jam.</span>
    </div>
  </div>

  <div class="row g-4">

    
    <div class="col-lg-7">
      <div class="card-section h-100">

        <h5>Booking Details</h5>

        <div class="row">
          <div class="col-md-6">

            <div class="section-title">NAMA</div>
            <div class="section-value"><?php echo e($pengunjung->nama ?? $pengunjung->nama_pic ??'-'); ?></div>

            <div class="section-title">NO TELEPON</div>
            <div class="section-value"><?php echo e($pengunjung->no_telp ?? $pengunjung->no_telp_pic ?? '-'); ?></div>

            <div class="section-title">RESERVATION</div>
            <div class="section-value"><?php echo e($durasi ?? '-'); ?> hari</div>

          </div>

          <div class="col-md-6">

            <div class="section-title">CHECK-IN</div>
            <div class="section-value">
              <?php echo e(\Carbon\Carbon::parse($pengunjung->check_in)->translatedFormat('l, d F Y')); ?> <br><b>12.00 WIB</b>
            </div>

            <div class="section-title">CHECK-OUT</div>
            <div class="section-value">
              <?php echo e(\Carbon\Carbon::parse($pengunjung->check_out)->translatedFormat('l, d F Y')); ?> <br><b>12.00 WIB</b>
            </div>

          </div>
        </div>

        <hr>

        
        <h5 class="mb-3 fw-bold">Method Payments</h5>

        <div class="accordion-item">
            <label class="accordion-btn">
                <input type="radio" name="metode" value="Via ATM" class="payment-select" style="margin-right:6px;">
                Via ATM
            </label>
            <div class="accordion-content">
                <ol>
                    <li>Masukkan kartu ATM dan PIN Anda.</li>
                    <li>Pilih menu "Transfer" → "Bank Lain" / "Transfer Antar Bank".</li>
                    <li>Masukkan kode bank Bukopin (441) + nomor rekening tujuan.</li>
                    <li>Masukkan jumlah uang yang ingin ditransfer.</li>
                    <li>Cek data → pilih "Benar" / "Ya".</li>
                </ol>
            </div>
        </div>

        <div class="accordion-item">
            <label class="accordion-btn">
                <input type="radio" name="metode" value="Via Mobile Banking" class="payment-select" style="margin-right:6px;">
                Via Mobile Banking
            </label>
            <div class="accordion-content">
                <ol>
                    <li>Buka aplikasi mobile banking Bukopin & login.</li>
                    <li>Pilih menu "Transfer Dana" / "Transfer Antar Bank".</li>
                    <li>Masukkan kode bank (441) + nomor rekening tujuan.</li>
                    <li>Masukkan jumlah uang.</li>
                    <li>Konfirmasi → masukkan PIN.</li>
                </ol>
            </div>
        </div>

        <div class="accordion-item">
            <label class="accordion-btn">
                <input type="radio" name="metode" value="Via Cash" class="payment-select" style="margin-right:6px;">
                Via Cash (Bayar di Tempat)
            </label>
            <div class="accordion-content">
                <ol>
                    <li>Datang ke kasir setelah reservasi.</li>
                    <li>Sebutkan nama pemesan / bukti reservasi.</li>
                    <li>Bayar sesuai nominal.</li>
                    <li>Terima struk sebagai bukti.</li>
                </ol>
            </div>
        </div>

        
        <div id="uploadSection">
          <form class="upload-box mt-4"
                action="<?php echo e(route('booking.payment.upload', $pengunjung->id)); ?>"
                method="POST"
                enctype="multipart/form-data"
                id="paymentForm">

            <?php echo csrf_field(); ?>
            
            <input type="hidden" name="metode_pembayaran" id="payment_method_upload">
            
            <label class="fw-bold mb-1">Upload Bukti Pembayaran</label>
            <input type="file" name="bukti_pembayaran" id="buktiFile" accept=".jpg,.jpeg,.png,.pdf" required>
            <small class="text-muted">Format: JPG, PNG, PDF (Max 2MB).</small>

            <div class="mt-3">
              <button type="submit" class="btn-maroon">Kirim Bukti</button>
            </div>
          </form>
        </div>

        
        <div id="cashButton" class="mt-4">
            <form action="<?php echo e(route('booking.payment.cash', $pengunjung->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="metode_pembayaran" value="Via Cash">
                <button class="btn-maroon w-100 d-block">BOOKING NOW (BAYAR DI TEMPAT)</button>
            </form>
        </div>

      </div>
    </div>

    
    <div class="col-lg-5">
      <div class="card-section h-100">

        <h5>Booking Summary</h5>
        <div class="border rounded p-3 mb-3">
          <div class="row">
            <div class="col-6">
              <div class="fw-bold">CHECK-IN</div>
              <div><?php echo e(\Carbon\Carbon::parse($pengunjung->check_in)->translatedFormat('l, d F Y')); ?><br><b>12.00 WIB</b></div>
            </div>
            <div class="col-6">
              <div class="fw-bold">CHECK-OUT</div>
              <div><?php echo e(\Carbon\Carbon::parse($pengunjung->check_out)->translatedFormat('l, d F Y')); ?><br><b>12.00 WIB</b></div>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-6">
              <div class="fw-bold">ROOM SELECTED</div>
              <div><?php echo e($kamar->jenis_kamar ?? '-'); ?></div>
            </div>
            <div class="col-6">
              <div class="fw-bold">RESERVATION</div>
              <div><?php echo e($durasi ?? '-'); ?> hari</div>
            </div>
          </div>
        </div>

        <h5>Price Summary</h5>
        <div class="d-flex justify-content-between">
          <span>Hunian (<?php echo e($kamar->jenis_kamar); ?>)</span>
          <span>Rp<?php echo e(number_format($kamar->harga,0,',','.')); ?></span>
        </div>

        <small class="text-muted d-block mb-2">
            <?php echo e($pengunjung->jumlah_kamar); ?> kamar ×
            <?php echo e($durasi); ?> hari ×
            Rp<?php echo e(number_format($kamar->harga,0,',','.')); ?>

            = <strong>Rp<?php echo e(number_format($totalKamar,0,',','.')); ?></strong>
        </small>
        <hr>

        <div class="d-flex justify-content-between fw-bold">
          <span>Total</span>
          <span>Rp<?php echo e(number_format($totalPembayaran,0,',','.')); ?></span>
        </div>

        <div class="mt-3">
          <a href="<?php echo e(url('/')); ?>" class="btn-maroon w-100 text-center" style="display:block; border-radius:12px;">
              Kembali ke Halaman Utama
          </a>
        </div>

      </div>
    </div>

  </div>
</div>


<script>
document.querySelectorAll(".payment-select").forEach(radio => {
    radio.addEventListener("change", function () {
        const selectedValue = this.value; // Contoh: "Via ATM" atau "Via Cash"
        const upload = document.getElementById("uploadSection");
        const cashBtn = document.getElementById("cashButton");
        const contents = document.querySelectorAll(".accordion-content");

        // 1. Tampilkan Accordion
        contents.forEach(c => c.classList.remove("show"));
        this.closest(".accordion-item").querySelector(".accordion-content").classList.add("show");

        // 2. Logika Tampilan Tombol
        if (selectedValue === "Via Cash") {
            // Jika Cash -> Sembunyikan Upload, Tampilkan Tombol Cash
            upload.style.display = "none";
            cashBtn.style.display = "block";
        } else {
            // Jika ATM/M-Banking -> Tampilkan Upload, Sembunyikan Tombol Cash
            upload.style.display = "block";
            cashBtn.style.display = "none";
            
            // Masukkan nilai yang dipilih ke dalam input hidden form upload
            document.getElementById("payment_method_upload").value = selectedValue;
        }
    });
});

// Validasi File Upload (SweetAlert)
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    const fileInput = document.getElementById('buktiFile');

    if(form) {
        form.addEventListener('submit', function(e) {
            if (!fileInput.files.length) {
                e.preventDefault();
                alert('Silakan pilih file bukti pembayaran.');
                return false;
            }
            const file = fileInput.files[0];
            if (file.size > 2 * 1024 * 1024) { // 2MB
                e.preventDefault();
                alert('Ukuran file maksimal 2MB.');
                return false;
            }
        });
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\peng\resources\views/booking/payment.blade.php ENDPATH**/ ?>