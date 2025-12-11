<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo e($meta['title'] ?? 'Laporan Bulanan'); ?></title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 11px; color: #222; }
    .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #7b1a2e; padding-bottom: 10px; }
    .header h1 { margin: 0; font-size: 18px; color: #7b1a2e; }
    .header p { margin: 4px 0; color: #666; font-size: 10px; }

    .stats-grid { display: table; width: 100%; margin: 15px 0; }
    .stat-box { display: table-cell; width: 25%; padding: 10px; text-align: center; border: 1px solid #ddd; background: #f9f9f9; }
    .stat-value { font-size: 20px; font-weight: bold; color: #7b1a2e; }
    .stat-label { font-size: 9px; color: #666; margin-top: 4px; }

    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th { background: #7b1a2e; color: white; padding: 8px 6px; text-align: left; font-size: 10px; }
    td { border: 1px solid #ddd; padding: 6px; font-size: 10px; }
    tr:nth-child(even) { background: #f9f9f9; }

    .footer { margin-top: 20px; font-size: 9px; color: #999; text-align: center; }

    .badge { padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; display: inline-block; }
    .badge-corporate { background: #dbeafe; color: #1e40af; }
    .badge-individu { background: #fef3c7; color: #92400e; }
    .badge-lunas { background: #d1fae5; color: #065f46; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
  </style>
</head>

<body>

  <div class="header">
    <h1><?php echo e($meta['title'] ?? 'Laporan Bulanan'); ?></h1>
    <p>Periode: <?php echo e($meta['month'] ?? '-'); ?></p>
    <p>Dicetak pada: <?php echo e($meta['date'] ?? now()->format('d M Y H:i')); ?></p>
  </div>

  <!-- Stats Summary -->
  <div class="stats-grid">
    <div class="stat-box">
      <div class="stat-value"><?php echo e($meta['total_kamar'] ?? 0); ?></div>
      <div class="stat-label">Total Kamar</div>
    </div>
    <div class="stat-box">
      <div class="stat-value"><?php echo e($meta['kamar_kosong'] ?? 0); ?></div>
      <div class="stat-label">Kamar Kosong</div>
    </div>
    <div class="stat-box">
      <div class="stat-value"><?php echo e($meta['kamar_terisi'] ?? 0); ?></div>
      <div class="stat-label">Kamar Terisi</div>
    </div>
    <div class="stat-box">
      <div class="stat-value"><?php echo e($meta['total_booking'] ?? 0); ?></div>
      <div class="stat-label">Total Booking</div>
    </div>
  </div>

  <!-- Booking Table -->
  <h3 style="margin-top:20px; color:#7b1a2e; font-size:12px;">Daftar Booking</h3>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Jenis</th>
        <th>Check-in</th>
        <th>Check-out</th>
        <th>Kamar</th>
        <th>Metode Bayar</th>
        <th>Bukti Pembayaran</th>
        <th>Status</th>
        <th>No. Telp</th>
        <th>Jml Kamar</th>
      </tr>
    </thead>

    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td style="text-align:center;"><?php echo e($i+1); ?></td>

        <td>
          <strong>
            <?php if(strtolower($b->jenis_tamu) == 'corporate' && $b->nama_pic): ?>
              <?php echo e($b->nama_pic); ?>

              <?php if($b->nama): ?>
                <br><small style="color:#666;font-weight:normal"><?php echo e($b->nama); ?></small>
              <?php endif; ?>
            <?php else: ?>
              <?php echo e($b->nama); ?>

            <?php endif; ?>
          </strong>
        </td>

        <td>
          <span class="badge <?php echo e(strtolower($b->jenis_tamu)=='corporate'?'badge-corporate':'badge-individu'); ?>">
            <?php echo e(ucfirst($b->jenis_tamu)); ?>

          </span>
        </td>

        <td><?php echo e(\Carbon\Carbon::parse($b->check_in)->format('d M Y')); ?></td>
        <td><?php echo e(\Carbon\Carbon::parse($b->check_out)->format('d M Y')); ?></td>
        <td><?php echo e($b->kode_kamar ?? '-'); ?></td>
        <td><?php echo e($b->metode_pembayaran ?? '-'); ?></td>

        <td>
          <?php if($b->bukti_pembayaran): ?>
              <img src="<?php echo e(asset('storage/bukti_pembayaran/' . $b->bukti_pembayaran)); ?>"
                   alt="Bukti Pembayaran"
                   style="width:60px; height:auto; border:1px solid #ccc;">
          <?php else: ?>
              -
          <?php endif; ?>
        </td>

        <td>
          <span class="badge 
            <?php if($b->payment_status=='lunas' || $b->payment_status=='paid'): ?> badge-lunas
            <?php elseif($b->payment_status=='pending'): ?> badge-pending
            <?php elseif($b->payment_status=='rejected'): ?> badge-rejected
            <?php endif; ?>">
            <?php if($b->payment_status=='lunas' || $b->payment_status=='paid'): ?> Lunas
            <?php elseif($b->payment_status=='pending'): ?> Pending
            <?php elseif($b->payment_status=='konfirmasi_booking'): ?> Konfirmasi
            <?php elseif($b->payment_status=='rejected'): ?> Rejected
            <?php else: ?> <?php echo e(ucfirst($b->payment_status)); ?>

            <?php endif; ?>
          </span>
        </td>

        <td><?php echo e($b->no_telp_pic ?? $b->no_telp ?? '-'); ?></td>

        <td style="text-align:center;"><?php echo e($b->jumlah_kamar ?? 1); ?></td>
      </tr>

      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr>
        <td colspan="11" style="text-align:center; padding:20px; color:#999;">
          Tidak ada booking pada periode ini
        </td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <?php if($bookings->count() > 0): ?>
  <div style="margin-top:20px; padding:10px; background:#f9f9f9; border:1px solid #ddd;">
    <strong>Ringkasan:</strong><br>
    Total Booking: <?php echo e($bookings->count()); ?> |
    Corporate: <?php echo e($bookings->where('jenis_tamu','corporate')->count()); ?> |
    Individu: <?php echo e($bookings->where('jenis_tamu','individu')->count()); ?> |
    Lunas: <?php echo e($bookings->whereIn('payment_status',['lunas','paid'])->count()); ?>

  </div>
  <?php endif; ?>

  <div class="footer">
    <p>Dokumen ini digenerate otomatis oleh Sistem Penginapan</p>
    <p>&copy; <?php echo e(now()->format('Y')); ?> - Sistem Manajemen Penginapan</p>
  </div>
</body>
</html>
  <?php /**PATH C:\xampp\htdocs\peng\resources\views/admin/report/monthly_pdf.blade.php ENDPATH**/ ?>