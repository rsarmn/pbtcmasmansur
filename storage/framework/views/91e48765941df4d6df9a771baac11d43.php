<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Berhasil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    :root {
        --peach: #f1cfc4;
        --primary: #a0203c;
        --light: #fbf7f6;
        --white: #fff;
        --success: #10b981;
    }

    html, body {
        height: 100%;
        margin: 0;
    }

    body {
        background: var(--light) !important;
        font-family: "Segoe UI", sans-serif;
    }

    .container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .success-card {
        background: var(--white);
        border: 3px solid var(--primary);
        border-radius: 20px;
        padding: 40px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 10px 30px rgba(160, 32, 60, 0.15);
        text-align: center;
        animation: slideUp 0.5s ease-out;
        margin: auto;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .success-icon {
        width: 90px;
        height: 90px;
        background: var(--success);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        animation: scaleIn 0.6s ease-out 0.2s both;
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    }

    @keyframes scaleIn {
        from { transform: scale(0); }
        to { transform: scale(1); }
    }

    .success-icon svg {
        width: 50px;
        height: 50px;
        stroke: white;
        stroke-width: 3;
        stroke-linecap: round;
        stroke-linejoin: round;
        fill: none;
        stroke-dasharray: 100;
        stroke-dashoffset: 100;
        animation: drawCheck 0.8s ease-out 0.5s forwards;
    }

    @keyframes drawCheck {
        to { stroke-dashoffset: 0; }
    }

    .success-card h2 {
        font-size: 28px;
        font-weight: 800;
        color: #111;
        margin-bottom: 12px;
    }

    .subtitle {
        color: #333;
        font-size: 14px;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .info-grid {
        background: var(--peach);
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 30px;
        text-align: left;
        border: 2px solid rgba(160, 32, 60, 0.2);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid rgba(160, 32, 60, 0.15);
        gap: 15px;
    }

    .info-label {
        font-weight: 600;
        font-size: 14px;
        color: #333;
    }

    .info-value {
        font-weight: 700;
        font-size: 14px;
        color: #111;
        text-align: right;
    }

    .btn-home {
        background: var(--primary);
        color: var(--white);
        border: none;
        padding: 14px 32px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 16px;
        width: 100%;
        display: inline-block;
        transition: .3s;
        text-decoration: none;
    }

    .btn-home:hover {
        transform: translateY(-2px);
        background:#8a1a32;
        text-decoration:none;
    }

    .btn-download {
        background:#0562a8;
        color:white;
        border:none;
        padding:14px 32px;
        border-radius:12px;
        font-weight:700;
        width:100%;
        margin-bottom:14px;
        display:inline-block;
        text-decoration:none;
    }

    .btn-download:hover{
        background:#034c82;
        text-decoration:none;
        transform:translateY(-2px);
    }

    .status-badge {
        display: inline-block;
        padding: 8px 20px;
        background: #fef3c7;
        color: #92400e;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 20px;
        border: 2px solid #fbbf24;
    }
</style>

<div class="success-card">
    <div class="success-icon">
        <svg viewBox="0 0 52 52">
            <path d="M14 27l8 8 16-16"/>
        </svg>
    </div>

    <h2>Booking Berhasil!</h2>

    <div class="status-badge">Menunggu Konfirmasi</div>

    <p class="subtitle">
        Terima kasih telah melakukan booking. Pesanan Anda sedang diproses dan menunggu konfirmasi dari admin.
    </p>

    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">Nama</span>
            <span class="info-value">
                <?php if($pengunjung->jenis_tamu == 'corporate'): ?>
                    <?php echo e($pengunjung->nama_pic ?? $pengunjung->nama); ?>

                <?php else: ?>
                    <?php echo e($pengunjung->nama); ?>

                <?php endif; ?>
            </span>
        </div>

        <div class="info-row">
            <span class="info-label">Jenis Booking</span>
            <span class="info-value"><?php echo e(ucfirst($pengunjung->jenis_tamu)); ?></span>
        </div>

        <div class="info-row">
            <span class="info-label">Check-in</span>
            <span class="info-value"><?php echo e(\Carbon\Carbon::parse($pengunjung->check_in)->format('d M Y')); ?></span>
        </div>

        <div class="info-row">
            <span class="info-label">Check-out</span>
            <span class="info-value"><?php echo e(\Carbon\Carbon::parse($pengunjung->check_out)->format('d M Y')); ?></span>
        </div>

        <div class="info-row">
            <span class="info-label">Total Pembayaran</span>
            <span class="info-value">Rp <?php echo e(number_format($totalPembayaran, 0, ',', '.')); ?></span>
        </div>

        <div class="info-row">
            <span class="info-label">Tanggal Booking</span>
            <span class="info-value"><?php echo e(\Carbon\Carbon::parse($pengunjung->created_at)->format('d M Y, H:i')); ?></span>
        </div>
    </div>

    <!-- TOMBOL DOWNLOAD VOUCHER -->
    <a href="<?php echo e(route('booking.voucher', $pengunjung->id)); ?>" class="btn-download">
        Download Bukti Invoice
    </a>

    <!-- TOMBOL KEMBALI -->
    <a href="<?php echo e(url('/')); ?>" class="btn-home">
        Kembali ke Beranda
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\peng\resources\views/booking/success.blade.php ENDPATH**/ ?>