@extends('layout')

@section('content')
<style>
  .stat-card{
    background:rgba(179,18,59,.35);
    color:#fff;
    border-radius:28px;
    padding:32px 20px;
    text-align:center;
    box-shadow:0 12px 30px rgba(179,18,59,.15);
    backdrop-filter:blur(2px);
  }
  .stat-icon{
    width:72px;
    height:72px;
    border-radius:20px;
    background:rgba(255,255,255,.25);
    margin:0 auto 14px;
    display:grid;
    place-items:center;
  }
  .stat-value{
    font-size:32px;
    font-weight:800;
    letter-spacing:.3px;
    margin-bottom:6px;
  }
  .stat-label{
    opacity:.95;
    font-weight:500;
    font-size:15px;
  }
</style>

<div class="text-center pt-6">
  <p class="text-sm text-gray-600 max-w-5xl mx-auto mb-4">Ringkasan cepat hari ini — angka-angka di bawah menunjukkan kondisi saat ini: jumlah peserta (total orang), check-in yang terjadi hari ini, dan rasio keterisian kamar.</p>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 max-w-5xl mx-auto">
    
    <!-- Kamar Tersedia -->
    <div class="stat-card">
      <div class="stat-icon">
        <img src="{{ asset('img/chart.png') }}" alt="Chart" class="w-9 h-9">
      </div>
      <div class="stat-value">{{ $kamarKosong }}</div>
      <div class="stat-label">Kamar tersedia
        <div style="font-size:12px;opacity:.9">Total kamar kosong saat ini</div>
      </div>
    </div>

    <!-- Kamar Terisi -->
    <div class="stat-card" style="background:rgba(179,18,59,.45)">
      <div class="stat-icon">
        <img src="{{ asset('img/chart.png') }}" alt="Chart" class="w-9 h-9">
      </div>
      <div class="stat-value">{{ $kamarTerisi }}</div>
      <div class="stat-label">Kamar terisi</div>
      <div style="margin-top:10px">
        <div style="background:#f3f4f6;border-radius:12px;height:12px;max-width:170px;margin:6px auto 4px;overflow:hidden">
          <div style="width:{{ $occupancyRate }}%;background:rgba(255,255,255,0.9);height:12px"></div>
        </div>
        <div style="font-size:12px;opacity:.95">Occupancy: <strong>{{ $occupancyRate }}%</strong></div>
      </div>
    </div>

    <!-- Jumlah Pengunjung (hari ini) -->
    <div class="stat-card" style="background:rgba(179,18,59,.5)">
      <div class="stat-icon">
        <img src="{{ asset('img/chart.png') }}" alt="Chart" class="w-9 h-9">
      </div>
      <div class="stat-value">{{ number_format($jumlahPengunjung ?? 0) }}</div>
      <div class="stat-label">Jumlah pengunjung (hari ini)
        <div style="font-size:12px;opacity:.9">Total peserta dari semua booking aktif hari ini <span title="Jumlah peserta = nilai field jumlah_peserta pada masing-masing booking">ℹ️</span></div>
      </div>
    </div>
    
    <!-- Check-ins Hari Ini -->
    <div class="stat-card" style="background:rgba(179,18,59,.4)">
      <div class="stat-icon">
        <img src="{{ asset('img/chart.png') }}" alt="Chart" class="w-9 h-9">
      </div>
      <div class="stat-value">{{ number_format($checkinHariIni ?? 0) }}</div>
      <div class="stat-label">Check-ins (hari ini)
        <div style="font-size:12px;opacity:.9">Peserta yang melakukan check-in hari ini <span title="Angka ini menghitung peserta pada pengunjung yang statusnya sudah check-in hari ini">ℹ️</span></div>
      </div>
    </div>
    
  </div>

  <!-- Quick Access Buttons -->
  <div class="mt-6 flex flex-wrap gap-3 justify-center">
    <a href="{{ route('kamar.index') }}" class="px-6 py-3 bg-purple-500 text-white rounded-lg font-semibold hover:bg-purple-600 transition shadow-lg">
      Data Kamar
    </a>
    <a href="{{ route('pengunjung.index') }}" class="px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition shadow-lg">
      Data Pengunjung
    </a>
    <a href="{{ route('pembayaran.konfirmasi') }}" class="px-6 py-3 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition shadow-lg">
      Pembayaran
    </a>
    <a href="{{ route('report.monthly') }}" class="px-6 py-3 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition shadow-lg">
      Report
    </a>
  </div>
</div>
 
<!-- Recent activity -->
<div class="max-w-5xl mx-auto mt-6">
  <div class="bg-white rounded-2xl p-4 shadow flex flex-col md:flex-row gap-4">
    <div class="flex-1">
      <h4 class="text-sm font-bold text-[var(--brand)] mb-2">Recent Activity</h4>
      <ul class="divide-y">
        @forelse($recentBookings as $b)
        <li class="py-2">
          <div class="text-sm font-medium">
            @if(strtolower($b->jenis_tamu) == 'corporate')
              {{ $b->nama_pic ?? 'PIC' }} <span class="text-xs text-gray-500">({{ $b->nama }})</span>
            @else
              {{ $b->nama }}
            @endif
          </div>
          <div class="text-xs text-gray-500">Booked: {{ $b->kode_kamar ?? '-' }} — {{ \Illuminate\Support\Carbon::parse($b->created_at)->format('d M Y H:i') }}</div>
        </li>
        @empty
        <li class="py-2 text-sm text-gray-500">No recent activity.</li>
        @endforelse
      </ul>
    </div>
    <div class="w-64">
      <h4 class="text-sm font-bold text-[var(--brand)] mb-2">Today / Upcoming</h4>
      <div class="text-sm">Check-ins today: <span class="font-semibold">{{ $todayCheckins->count() }}</span></div>
      <div class="mt-3 text-sm font-medium">Upcoming Check-outs</div>
      <ul class="mt-2 text-sm text-gray-700">
        @forelse($upcomingCheckouts as $u)
        <li class="py-1">
          @if(strtolower($u->jenis_tamu) == 'corporate')
            {{ $u->nama_pic ?? 'PIC' }}
          @else
            {{ $u->nama }}
          @endif
          — {{ $u->check_out }}
        </li>
        @empty
        <li class="text-gray-500 py-1">None</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>
@endsection
