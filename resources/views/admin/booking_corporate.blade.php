@extends('layout')
@section('content')
<div class="container mt-5">
  <h3>Form Booking Corporate</h3>
  <form action="{{ route('booking.storeCorporate') }}" method="POST">@csrf
    <div class="row">
      <div class="col-md-6">
        <label>Nama Kegiatan</label>
        <input name="nama_kegiatan" class="form-control" required>
        <label>Nama PIC</label>
        <input name="nama_pic" class="form-control">
        <label>No Telp PIC</label>
        <input name="no_telp_pic" class="form-control">
        <label>Tanggal</label>
        <input type="date" name="check_in" class="form-control">
        <input type="date" name="check_out" class="form-control mt-2">
      </div>
      <div class="col-md-6">
        <label>Jumlah Peserta</label>
        <input name="jumlah_peserta" class="form-control">
        <label>Jumlah Kamar</label>
        <input name="jumlah_kamar" class="form-control">
        <label class="mt-2">Kebutuhan konsumsi - Snack (contoh: 20 porsi) <small style="color:#666">(kosong jika tidak)</small></label>
        <input name="kebutuhan_snack" class="form-control" placeholder="jumlah porsi atau keterangan">
        <label class="mt-2">Kebutuhan konsumsi - Makan (contoh: 20 porsi) <small style="color:#666">(kosong jika tidak)</small></label>
        <input name="kebutuhan_makan" class="form-control" placeholder="jumlah porsi atau keterangan">
        <label>Special Request</label>
        <textarea name="special_request" class="form-control"></textarea>
      </div>
    </div>
    <button class="btn btn-primary mt-3">Simpan Booking</button>
  </form>
</div>
@endsection
