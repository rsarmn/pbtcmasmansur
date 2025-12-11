<?php $__env->startSection('content'); ?>
<style>
  .section-header{background:var(--brand);color:#fff;border-radius:18px;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
  .pill-btn{background:#fff;color:var(--brand);border:0;border-radius:999px;padding:8px 16px;font-weight:700;text-decoration:none}
  .pill-btn:hover{filter:brightness(95%)}
  .table-shell{background:rgba(179,18,59,.08); border-radius:18px; padding:18px}
  .data-table{width:100%;border-collapse:collapse}
  .data-table thead th{background:#f5d7de;padding:12px;text-align:left;color:#7b1a2e;font-weight:600;border-bottom:2px solid #e8c5d0}
  .data-table tbody td{background:#fff;padding:12px;border-bottom:1px solid #f0e1e5}
  .data-table tbody tr:last-child td{border-bottom:0}
  .btn-delete{background:#dc3545;color:#fff;border:0;padding:6px 12px;border-radius:6px;font-size:13px;cursor:pointer}
  .btn-delete:hover{background:#c82333}
  .aksi-menu{z-index:100}
  .aksi-menu.show{display:block!important}
  /* keep table cell background white, add a left accent instead of full-row background */
  .checked-out-row td{color:#7a0b0b}
  .checked-out-row{border-left:4px solid #ef4444}
  .checked-out-badge{background:#f87171;color:#fff;padding:4px 8px;border-radius:8px;font-size:12px;font-weight:700}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.pill-btn').forEach(btn => {
    if(btn.textContent.includes('Aksi')) {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const menu = this.nextElementSibling;
        document.querySelectorAll('.aksi-menu').forEach(m => m.classList.remove('show'));
        menu.classList.toggle('show');
      });
    }
  });
  document.addEventListener('click', () => {
    document.querySelectorAll('.aksi-menu').forEach(m => m.classList.remove('show'));
  });

  // Handle delete with SweetAlert
  document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      Swal.fire({
        title: 'Hapus Data?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: '✖️ Batal',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
          Swal.fire({
            icon: 'success',
            title: 'Menghapus...',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500
          });
        }
      });
    });
  });
});
</script>



<div class="table-shell">
  <div style="font-weight:600;margin-bottom:12px;color:#7b1a2e">DATA PENGUNJUNG PENGINAPAN</div>
  <div style="overflow-x:auto">
    <?php $today = \Illuminate\Support\Carbon::now(); ?>
    <table class="data-table">
      <thead>
        <tr>
          <th>Nama</th>
          <th>Identitas</th>
          <th>No Identitas</th>
          <th>Jenis Tamu</th>
          <th>Check-in</th>
          <th>Check-out</th>
          <th>Kode Kamar</th>
          <th>Kamar Dipilih</th>
          <th>Jumlah Kamar</th>
          <th>Jumlah Orang</th>
          <th>Total Harga</th>
          <th>Bayar</th>
          <th>Keterangan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $activeList = collect();
$checkedList = collect();

foreach ($pengunjungs as $p) {

    $today = now()->format('Y-m-d');

    // Checkout otomatis (tanggal reservasi)
    $isAutoCheckout = $p->check_out <= $today;

    // Checkout manual admin
    $isManualCheckout = $p->checked_out == 1;

    // Hasil akhir
    $isCheckedOut = $isAutoCheckout || $isManualCheckout;

    if ($isCheckedOut) {
        $checkedList->push($p);
    } else {
        $activeList->push($p);
    }
}
        ?>

          <?php if($activeList->count() + $checkedList->count() == 0): ?>
          <tr><td colspan="13" style="text-align:center;color:#999;padding:24px">Belum ada data.</td></tr>
        <?php else: ?>
          
          <?php $__currentLoopData = $activeList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
    $today = now()->format('Y-m-d');

    $isAutoCheckout = $p->check_out <= $today;

    $isManualCheckout = $p->checked_out == 1;

    $isCheckedOut = $isAutoCheckout || $isManualCheckout;

    $checkoutDate = $isManualCheckout ? now()->format('Y-m-d') : $p->check_out;
?>

            <tr class="<?php echo e($isCheckedOut ? 'checked-out-row' : ''); ?>">
          <td>
            <?php if($p->jenis_tamu == 'corporate'): ?>
              <strong><?php echo e($p->nama_pic ?? 'PIC'); ?></strong>
              <?php if($p->nama): ?>
                <br><small style="color:#666"><?php echo e($p->nama); ?></small>
              <?php endif; ?>
            <?php else: ?>
              <?php echo e($p->nama); ?>

            <?php endif; ?>
          </td>
          <td><?php echo e(strtoupper($p->identity_type ?? 'KTP')); ?></td>
          <td><?php echo e($p->no_identitas); ?></td>
          <td><span style="background:<?php echo e($p->jenis_tamu == 'corporate' ? '#28a745' : '#007bff'); ?>;color:#fff;padding:4px 10px;border-radius:12px;font-size:12px"><?php echo e(ucfirst($p->jenis_tamu)); ?></span></td>
          <td><?php echo e($p->check_in); ?></td>
          <td><?php echo e($p->check_out); ?></td>
          <td><?php echo e($p->kode_kamar); ?></td>
          <td>
            <?php
              $kamarIds = explode(',', $p->kode_kamar ?? '');
              $jenisKamars = [];
              foreach ($kamarIds as $kamarId) {
                $kamar = \App\Models\Kamar::where('kode_kamar', trim($kamarId))->first();
                if ($kamar && !in_array($kamar->jenis_kamar, $jenisKamars)) {
                  $jenisKamars[] = $kamar->jenis_kamar;
                }
              }
            ?>
            <strong><?php echo e(implode(', ', $jenisKamars) ?: '-'); ?></strong>
          </td>
          <td><strong><?php echo e($p->jumlah_kamar ?? '-'); ?></strong></td>
          <td><strong><?php echo e($p->jumlah_peserta ?? '-'); ?></strong></td>
          <?php $displayTotal = $p->display_total ?? null; ?>
          <td><strong style="color:#0b6623"><?php echo e($displayTotal !== null ? 'Rp '.number_format($displayTotal,0,',','.') : '-'); ?></strong></td>
          <td><?php echo e($p->payment_status_label); ?></td>
          <td style="min-width:160px">
            <?php if($isCheckedOut): ?>
              <span class="checked-out-badge">Telah checkout</span>
              <div style="font-size:12px;color:#7a0b0b;margin-top:6px"><?php echo e($checkoutDate); ?></div>

            <?php else: ?>
              <span style="font-size:13px;color:#666">-</span>
            <?php endif; ?>
          </td>
          <td>
            <?php
              $phone = preg_replace('/[^0-9+]/','',$p->no_telp_pic ?? $p->no_telp ?? '');
              $wa = $phone ? 'https://wa.me/'.ltrim($phone,'0') : null;
            ?>
            <div style="display:flex;gap:6px;align-items:center">
              <?php if($wa): ?>
                <a href="<?php echo e($wa); ?>" target="_blank" class="pill-btn" style="background:#25D366;color:#fff;padding:6px 10px">Chat</a>
              <?php endif; ?>
                <div style="position:relative">
                <button class="pill-btn">Aksi ▾</button>
                <div style="position:absolute;right:0;background:#fff;border:1px solid #eee;padding:8px;border-radius:6px;display:none;min-width:160px" class="aksi-menu">
                  <a href="<?php echo e(route('pengunjung.show', $p->id)); ?>" style="display:block;padding:6px 8px;text-decoration:none;color:#333">👁️ View Detail</a>
                  <a href="<?php echo e(route('pengunjung.edit', $p->id)); ?>" style="display:block;padding:6px 8px;text-decoration:none;color:#333">✏️ Edit</a>
                  <form action="<?php echo e(route('pengunjung.destroy', $p->id)); ?>" method="GET" class="delete-form">
                    <button type="submit" style="width:100%;text-align:left;padding:6px 8px;border:0;background:none;cursor:pointer;color:#dc3545">🗑️ Hapus</button>
                  </form>
                </div>
              </div>
            </div>
          </td>
        </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

          
         <?php $__currentLoopData = $checkedList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $today = now()->format('Y-m-d');

        $isAutoCheckout = $p->check_out <= $today;
        $isManualCheckout = $p->checked_out == 1;

        $isCheckedOut = true; 
        $checkoutDate = $isManualCheckout ? now()->format('Y-m-d') : $p->check_out;
    ?>


<tr class="checked-out-row">

    <td>
      <?php if($p->jenis_tamu == 'corporate'): ?>
        <strong><?php echo e($p->nama_pic ?? 'PIC'); ?></strong>
        <?php if($p->nama): ?>
          <br><small style="color:#666"><?php echo e($p->nama); ?></small>
        <?php endif; ?>
      <?php else: ?>
        <?php echo e($p->nama); ?>

      <?php endif; ?>
    </td>

    <td><?php echo e(strtoupper($p->identity_type ?? 'KTP')); ?></td>
    <td><?php echo e($p->no_identitas); ?></td>
    <td>
      <span style="background:<?php echo e($p->jenis_tamu == 'corporate' ? '#28a745' : '#007bff'); ?>;color:#fff;padding:4px 10px;border-radius:12px;font-size:12px">
        <?php echo e(ucfirst($p->jenis_tamu)); ?>

      </span>
    </td>

    <td><?php echo e($p->check_in); ?></td>
    <td><?php echo e($p->check_out); ?></td>
    <td><?php echo e($p->kode_kamar); ?></td>

    <td>
      <?php
        $kamarIds = explode(',', $p->kode_kamar ?? '');
        $jenisKamars = [];
        foreach ($kamarIds as $kamarId) {
          $kamar = \App\Models\Kamar::where('kode_kamar', trim($kamarId))->first();
          if ($kamar && !in_array($kamar->jenis_kamar, $jenisKamars)) {
            $jenisKamars[] = $kamar->jenis_kamar;
          }
        }
      ?>
      <strong><?php echo e(implode(', ', $jenisKamars) ?: '-'); ?></strong>
    </td>

    <td><strong><?php echo e($p->jumlah_kamar ?? '-'); ?></strong></td>
    <td><strong><?php echo e($p->jumlah_peserta ?? '-'); ?></strong></td>

    <!-- ⭐ Tambahkan ini agar total harga muncul -->
    <td><strong style="color:#0b6623">
        <?php echo e($p->display_total !== null ? 'Rp '.number_format($p->display_total,0,',','.') : '-'); ?>

    </strong></td>

    <td><?php echo e($p->payment_status_label); ?></td>

    <td style="min-width:160px">
      <span class="checked-out-badge">Telah checkout</span>
      <div style="font-size:12px;color:#7a0b0b;margin-top:6px"><?php echo e($checkoutDate); ?></div>

    </td>

    <td>
      <?php
        $phone = preg_replace('/[^0-9+]/','',$p->no_telp_pic ?? $p->no_telp ?? '');
        $wa = $phone ? 'https://wa.me/'.ltrim($phone,'0') : null;
      ?>

      <div style="display:flex;gap:6px;align-items:center">
        <?php if($wa): ?>
          <a href="<?php echo e($wa); ?>" target="_blank" class="pill-btn" style="background:#25D366;color:#fff;padding:6px 10px">Chat</a>
        <?php endif; ?>

        <div style="position:relative">
          <button class="pill-btn">Aksi ▾</button>
          <div class="aksi-menu" style="position:absolute;right:0;background:#fff;border:1px solid #eee;padding:8px;border-radius:6px;display:none;min-width:160px">
            <a href="<?php echo e(route('pengunjung.show', $p->id)); ?>" style="padding:6px 8px;display:block;color:#333">👁️ View Detail</a>
            <a href="<?php echo e(route('pengunjung.edit', $p->id)); ?>" style="padding:6px 8px;display:block;color:#333">✏️ Edit</a>
            <form action="<?php echo e(route('pengunjung.destroy', $p->id)); ?>" method="GET" class="delete-form">
              <button style="padding:6px 8px;width:100%;text-align:left;background:none;border:0;color:#dc3545">🗑️ Hapus</button>
            </form>
          </div>
        </div>
      </div>
    </td>

</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\peng\resources\views/admin/pengunjung.blade.php ENDPATH**/ ?>