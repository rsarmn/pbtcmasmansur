@extends('layout')
@section('content')
<div style="padding:20px">
  <h2>System Status - User Check</h2>
  <p>Halaman ini menampilkan ringkasan singkat yang bisa digunakan untuk mengecek fungsionalitas sistem dari sisi user.</p>

  <div style="display:flex;gap:12px;margin-top:12px;flex-wrap:wrap">
    <div style="background:#fff;padding:16px;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.04)">
      <div style="font-size:24px;font-weight:700">{{ $totalKamar }}</div>
      <div style="color:#666">Total Kamar</div>
    </div>
    <div style="background:#fff;padding:16px;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.04)">
      <div style="font-size:24px;font-weight:700">{{ $kamarKosong }}</div>
      <div style="color:#666">Kamar Kosong</div>
    </div>
    <div style="background:#fff;padding:16px;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.04)">
      <div style="font-size:24px;font-weight:700">{{ $jumlahPengunjung }}</div>
      <div style="color:#666">Jumlah Pengunjung</div>
    </div>
    <div style="background:#fff;padding:16px;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.04)">
      <div style="font-size:24px;font-weight:700">{{ $pendingPayments }}</div>
      <div style="color:#666">Pembayaran Pending</div>
    </div>
  </div>

  <div style="margin-top:18px">
    <p>Link cepat (user-facing):</p>
    <ul>
      <li><a href="{{ route('booking.individu') }}">Booking Individu</a></li>
      <li><a href="{{ route('booking.corporate') }}">Booking Corporate</a></li>
      <li><a href="{{ route('pengunjung.index') }}">Data Pengunjung (admin)</a> - but this needs admin login</li>
    </ul>
  </div>
</div>
@endsection
