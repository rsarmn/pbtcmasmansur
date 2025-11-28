@extends('layout')
@section('content')
<div style="padding:20px">
  <h3>Check Ketersediaan - Kamar {{ $kamar->kode_kamar ?? $kamar->nomor_kamar }}</h3>

  <form method="GET" style="display:flex;gap:8px;align-items:center;margin-top:12px">
    <label>Start</label>
    <input type="date" name="start" value="{{ $start ?? '' }}">
    <label>End</label>
    <input type="date" name="end" value="{{ $end ?? '' }}">
    <button class="pill-btn" type="submit">Cek</button>
    <a href="{{ route('kamar.index') }}" class="pill-btn" style="background:#f3f3f3;color:#333">Kembali</a>
  </form>

  @if(!is_null($available))
    <div style="margin-top:12px">
      @if($available)
        <div style="background:#e6ffed;border:1px solid #b7f2c9;padding:12px;border-radius:8px">Kamar tersedia untuk rentang tanggal yang dipilih.</div>
      @else
        <div style="background:#fff4f6;border:1px solid #f2c2cb;padding:12px;border-radius:8px">Kamar TIDAK tersedia. Berikut booking yang bertumpuk:</div>
        <table style="width:100%;margin-top:12px;border-collapse:collapse">
          <thead>
            <tr style="background:#f5d7de;color:#7b1a2e"><th style="padding:8px">#</th><th style="padding:8px">Nama</th><th style="padding:8px">Check-in</th><th style="padding:8px">Check-out</th><th style="padding:8px">Status</th></tr>
          </thead>
          <tbody>
            @foreach($overlapping as $i => $o)
            <tr><td style="padding:8px">{{ $i+1 }}</td><td style="padding:8px">{{ $o->nama }}</td><td style="padding:8px">{{ $o->check_in }}</td><td style="padding:8px">{{ $o->check_out }}</td><td style="padding:8px">{{ $o->payment_status }}</td></tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>
  @endif

</div>
@endsection
