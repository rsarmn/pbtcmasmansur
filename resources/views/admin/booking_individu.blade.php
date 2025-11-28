@extends('layout')
@section('content')
<div style="padding:24px">
  <h3>Form Booking Individu</h3>
  <form action="{{ route('booking.individu.store') }}" method="POST">@csrf
    <div style="display:flex;gap:12px;flex-wrap:wrap">
      <div style="flex:1;min-width:260px">
        <label>Nama</label>
        <input name="nama" class="form-control" required>

        <label>No. Identitas (KTP/KTM/SIM)</label>
        <input name="no_identitas" class="form-control" required>

        <label>Tipe Identitas</label>
        <input name="identity_type" class="form-control" placeholder="KTP / KTM / SIM">
      </div>

      <div style="flex:1;min-width:260px">
        <label>Check-in</label>
        <input type="date" name="check_in" class="form-control" required>
        <label class="mt-2">Check-out</label>
        <input type="date" name="check_out" class="form-control" required>

        <label class="mt-2">Nomor Kamar (opsional)</label>
  <input name="kode_kamar" class="form-control">
      </div>
    </div>

    <div style="margin-top:12px">
      <label>Special Request</label>
      <textarea name="special_request" class="form-control"></textarea>
    </div>

    <div style="margin-top:12px">
      <button class="btn btn-primary">Simpan Booking</button>
    </div>
  </form>
</div>
@endsection
