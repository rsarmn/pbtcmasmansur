@extends('layout')
@section('content')
<div style="padding:20px">
  <h3>Detail Pengunjung: {{ $p->nama }}</h3>
  <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:12px">
    <div style="min-width:320px;background:#fff;padding:12px;border-radius:8px">
      <h4>Informasi Umum</h4>
      <p><strong>Nama:</strong> {{ $p->nama }}</p>
      <p><strong>Jenis Tamu:</strong> {{ $p->jenis_tamu }}</p>
  <p><strong>No Identitas:</strong> {{ $p->no_identitas ?? '-' }} <small>({{ $p->identity_type ?? '-' }})</small></p>
      <p><strong>Check-in:</strong> {{ $p->check_in }} <strong>Check-out:</strong> {{ $p->check_out }}</p>
  <p><strong>Nomor Kamar:</strong> {{ $p->kode_kamar ?? $p->nomor_kamar ?? '-' }}</p>
  <p><strong>Payment Status:</strong> {{ $p->payment_status_label ?? '-' }}</p>
    </div>

    <div style="min-width:320px;background:#fff;padding:12px;border-radius:8px">
      <h4>Corporate / PIC</h4>
      <p><strong>Nama Kegiatan:</strong> {{ $p->nama_kegiatan ?? '-' }}</p>
      <p><strong>Nama PIC:</strong> {{ $p->nama_pic ?? '-' }}</p>
      <p><strong>No HP PIC:</strong> {{ $p->no_telp_pic ?? '-' }}</p>
      <p><strong>Asal Persyarikatan:</strong> {{ $p->asal_persyarikatan ?? '-' }}</p>
      <p><strong>Jumlah Peserta:</strong> {{ $p->jumlah_peserta ?? '-' }}</p>
      <p><strong>Jumlah Kamar:</strong> {{ $p->jumlah_kamar ?? '-' }}</p>
    </div>

    <div style="flex:1 1 100%;background:#fff;padding:12px;border-radius:8px;margin-top:12px">
      <h4>Requests & Bukti</h4>
      <p><strong>Special Request:</strong><br>{{ $p->special_request ?? '-' }}</p>
      <p><strong>Kebutuhan Snack:</strong> {{ $p->kebutuhan_snack ?? '-' }} | <strong>Kebutuhan Makan:</strong> {{ $p->kebutuhan_makan ?? '-' }}</p>
  <p><strong>Bukti Identitas:</strong> 
    @if($p->bukti_identitas)
      @php $fileExists = file_exists(storage_path('app/' . $p->bukti_identitas)); @endphp
      @if($fileExists)
        <a href="{{ Storage::url(str_replace('public/','',$p->bukti_identitas)) }}" target="_blank">Lihat</a>
      @else
        <span style="color:#dc2626;">File tidak ditemukan</span>
      @endif
    @else - 
    @endif
  </p>
  <p><strong>Bukti Pembayaran:</strong> 
    @if($p->bukti_pembayaran)
      @php $fileExists = file_exists(storage_path('app/' . $p->bukti_pembayaran)); @endphp
      @if($fileExists)
        <a href="{{ Storage::url(str_replace('public/','',$p->bukti_pembayaran)) }}" target="_blank">Lihat</a>
      @else
        <span style="color:#dc2626;">File tidak ditemukan</span>
      @endif
    @else - 
    @endif
  </p>
    </div>
  </div>

  <div style="margin-top:20px;display:flex;gap:12px;flex-wrap:wrap">
    <a href="{{ route('pengunjung.index') }}" class="pill-btn" style="background:#6b7280;color:#fff">Kembali</a>
    
    @if(!$p->bukti_identitas)
      <a href="{{ route('pengunjung.checkin', $p->id) }}" class="pill-btn" style="background:#10b981;color:#fff">Check-In</a>
    @else
      <form action="{{ route('pengunjung.checkout', $p->id) }}" method="POST" onsubmit="return confirm('Proses checkout? Kamar akan dikembalikan ke status kosong.')" style="margin:0">
        @csrf
        <button type="submit" class="pill-btn" style="background:#ef4444;color:#fff">Check-Out</button>
      </form>
    @endif
    
    <a href="{{ route('pengunjung.edit', $p->id) }}" class="pill-btn" style="background:#f59e0b;color:#fff">Edit Data</a>
  </div>
</div>
@endsection
