<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Penginapan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{
      --brand:#b3123b; /* maroon-red from mock */
      --brand-2:#d23b57;
      --soft:#efb09b;
    }
    body{background:#fff;}
    /* Top header like the mock */
    .topbar-wrap{padding:22px 24px 0;}
    .topbar{
      background:var(--brand);
      color:#fff;
      border-radius:16px 16px 28px 28px;
      padding:18px 22px;
      position:relative;
      overflow:hidden;
    }
    .brand{font-weight:700; letter-spacing:.3px; display:flex; align-items:center; gap:10px;}
    .brand .logo{width:42px;height:42px;border-radius:50%;background:#fff1f4;color:var(--brand);display:grid;place-items:center;font-weight:800;}
    .nav-center{display:flex;gap:28px; align-items:center;}
    .nav-center a{color:#fff; text-decoration:none; font-weight:600; opacity:.95}
    .nav-center a:hover{opacity:1; text-decoration:underline;}
    .logout-pill button{background:#fff; color:var(--brand); border:0; padding:10px 18px; border-radius:999px; font-weight:800;}
    .logout-pill button:hover{filter:brightness(.95);}    

    /* Dashboard hero shared styles */
    .hero-area{position:relative; padding:24px 0 40px; min-height:320px;}
    .hero-blobs{position:absolute; inset:0; overflow:hidden; pointer-events:none;}
    .blob{position:absolute; border-radius:42% 58% 57% 43% / 52% 33% 67% 48%; background:var(--soft); opacity:.6; filter:saturate(110%);} 
    .blob.b1{left:-6%; top:8%; width:80%; height:52%; transform:rotate(-6deg); opacity:.55;}
    .blob.b2{right:-12%; top:6%; width:65%; height:45%; background:#ebb0c0; opacity:.55; filter:contrast(105%);} 
    .blob.b3{left:8%; bottom:-8%; width:72%; height:42%; background:#f0a080; opacity:.6;}

    /* Stat cards */
    .stat-card{background:rgba(179,18,59,.35); color:#fff; border-radius:28px; padding:28px 20px; text-align:center; box-shadow:0 12px 30px rgba(179,18,59,.15); backdrop-filter: blur(2px);} 
    .stat-icon{width:72px;height:72px;border-radius:20px; background:rgba(255,255,255,.25); margin:0 auto 14px; display:grid;place-items:center;}
    .stat-value{font-size:28px; font-weight:800; letter-spacing:.3px;}
    .stat-label{opacity:.95; font-weight:500;}

    @media (max-width: 768px){
      .nav-center{gap:18px;}
      .hero-area{padding-bottom:24px;}
      .stat-icon{width:64px;height:64px;}
    }
  </style>
</head>
<body>
  <div class="topbar-wrap">
    <header class="topbar">
      <div class="container-fluid d-flex align-items-center justify-content-between">
        <div class="brand">
          <img src="{{ asset('img/logo-pesma.png') }}" alt="Logo PESMA" style="width:42px;height:42px;border-radius:50%">
          <div class="brand-text">PESANTREN MAHASISWA<br><small style="font-size:0.85em">KH. MAS MANSUR</small></div>
        </div>
        <nav class="nav-center">
          <a href="{{ route('kamar.index') }}">Data Kamar</a>
          <a href="{{ route('pengunjung.index') }}">Data Pengunjung</a>
          <a href="{{ route('pengunjung.index') }}">Report Monthly</a>
        </nav>
        <div class="logout-pill">
          <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit">logout</button>
          </form>
        </div>
      </div>
    </header>
  </div>

  <main class="hero-area">
    <div class="hero-blobs" aria-hidden="true">
      <div class="blob b1"></div>
      <div class="blob b2"></div>
      <div class="blob b3"></div>
    </div>
    <div class="container">
      @yield('content')
    </div>
  </main>

</body>
</html>
