<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Penginapan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root{
      --brand:#b3123b;
      --brand-2:#d23b57;
      --soft:#efb09b;
    }
    .blob{
      position:absolute;
      border-radius:42% 58% 57% 43% / 52% 33% 67% 48%;
      background:var(--soft);
      opacity:.6;
      filter:saturate(110%);
    }
    .blob.b1{left:-6%; top:8%; width:80%; height:52%; transform:rotate(-6deg); opacity:.55;}
    .blob.b2{right:-12%; top:6%; width:65%; height:45%; background:#ebb0c0; opacity:.55; filter:contrast(105%);} 
    .blob.b3{left:8%; bottom:-8%; width:72%; height:42%; background:#f0a080; opacity:.6;}
  </style>
</head>
<body class="bg-white">

  <!-- Header -->
  <div class="pt-6 px-6">
    <header class="bg-[var(--brand)] text-white rounded-t-2xl rounded-b-[28px] px-6 py-5 shadow-lg relative overflow-hidden">
      <div class="flex items-center justify-between">
        
        <!-- Brand -->
        <div class="flex items-center gap-3">
          <a href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('img/logo-pesma.png') }}" alt="Logo" class="w-auto h-10 rounded-full">
          </a>
        </div>

        <!-- Navigation -->
        <nav class="flex gap-6 items-center">
          <a href="{{ route('kamar.index') }}" class="font-semibold opacity-90 hover:opacity-100 hover:underline">Data Kamar</a>
          <a href="{{ route('pengunjung.index') }}" class="font-semibold opacity-90 hover:opacity-100 hover:underline">Data Pengunjung</a>
          <a href="{{ route('report.monthly') }}" class="font-semibold opacity-90 hover:opacity-100 hover:underline">Report Monthly</a>
          <a href="{{ route('pengunjung.pending') }}" class="font-semibold opacity-90 hover:opacity-100 hover:underline">Pembayaran Pending</a>
        </nav>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('auth.logout') }}" class="ml-4">
          @csrf
          <button type="submit" class="bg-white text-[var(--brand)] font-extrabold px-5 py-2 rounded-full hover:brightness-95 transition">
            Logout
          </button>
        </form>
      </div>
    </header>
  </div>

  <!-- Hero Area -->
  <main class="relative py-10 min-h-[320px] overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
      <div class="blob b1"></div>
      <div class="blob b2"></div>
      <div class="blob b3"></div>
    </div>

    <div class="relative container mx-auto px-6">
      @yield('content')
    </div>
  </main>

</body>
</html>
