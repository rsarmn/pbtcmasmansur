<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            border-bottom: 2px solid #000000;
            padding-bottom: 10px;
            margin-bottom: 15px;
            text-align: center;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            color: #000000;
        }

        .section {
            margin-top: 18px;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 6px;
            color: #000000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        td {
            padding: 6px 4px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            width: 30%;
        }

        .footer {
            margin-top: 25px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .box {
            border: 1px solid #aaa;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .equal {
            width: 10px;
            font-weight: bold;
            text-align: center;
        }

    </style>
</head>

<body>
    <div style="width: 100%; text-align: center; margin: 0; padding: 0 0 15px 0;">
        <img src="<?php echo e(public_path('img/koppesma.jpeg')); ?>"
            alt="Header UMS"
            style="max-width:100%; height:100px; object-fit:contain; display:block; margin:0 auto;">
    </div>

    <div class="header">
        <div class="title">INVOICE</div>
        <small>ID Pemesanan: <?php echo e($pengunjung->id); ?></small>
    </div>

    <div class="section">
        <div class="section-title">Informasi Tamu</div>
        <div class="box">
            <table>
                <tr>
                    <td class="label">Nama Tamu</td>
                    <td class="equal">:</td>
                    <td><?php echo e($pengunjung->jenis_tamu == 'corporate' ? ($pengunjung->nama_pic ?? '-') : $pengunjung->nama); ?></td>
                </tr>
                <tr>
                    <td class="label">Jenis Booking</td>
                    <td class="equal">:</td>
                    <td><?php echo e(ucfirst($pengunjung->jenis_tamu)); ?></td>
                </tr>
                <tr>
                    <td class="label">Kontak</td>
                    <td class="equal">:</td>
                    <td><?php echo e($pengunjung->no_telp ?? $pengunjung->no_telp_pic ?? '-'); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detail Pemesanan</div>
        <div class="box">
            <table>
                <tr>
                    <td class="label">Tipe Kamar</td>
                    <td class="equal">:</td>
                    <td><?php echo e($kamar->jenis_kamar ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Check-in</td>
                    <td class="equal">:</td>
                    <td><?php echo e(\Carbon\Carbon::parse($pengunjung->check_in)->translatedFormat('l, d F Y')); ?></td>
                </tr>
                <tr>
                    <td class="label">Check-out</td>
                    <td class="equal">:</td>
                    <td><?php echo e(\Carbon\Carbon::parse($pengunjung->check_out)->translatedFormat('l, d F Y')); ?></td>
                </tr>
                <tr>
                    <td class="label">Durasi</td>
                    <td class="equal">:</td>
                    <td><?php echo e($durasi); ?> malam</td>
                </tr>
                <tr>
                    <td class="label">Jumlah Kamar</td>
                    <td class="equal">:</td>
                    <td><?php echo e($pengunjung->jumlah_kamar ?? 1); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Harga</div>
        <div class="box">
            <table>
                <tr>
                    <td class="label">Total Pembayaran</td>
                    <td class="equal">:</td>
                    <td><b>Rp <?php echo e(number_format($totalPembayaran, 0, ',', '.')); ?></b></td>
                </tr>
                <tr>
                    <td class="label">Tanggal Booking</td>
                    <td class="equal">:</td>
                    <td><?php echo e(\Carbon\Carbon::parse($pengunjung->created_at)->translatedFormat('d M Y, H:i')); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        <b>Invoice ini digunakan untuk check-in di Pesma KH. Mas Mansur UMS. Harap menunjukkan invoice dan kartu identitas saat kedatangan. Terima kasih telah memesan.</b><br>
    </div>

    <div style="margin-top:40px; text-align:left; font-size:13px;">
        <b>Pesma Inn</b><br>
            KH. Mas Mansur
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\peng\resources\views/booking/voucher.blade.php ENDPATH**/ ?>