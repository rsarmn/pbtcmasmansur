<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesma Booking System</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: "Segoe UI", sans-serif;
        }
        nav.navbar {
            background-color: #003366;
        }
        nav a.navbar-brand {
            color: white !important;
        }
        .container {
            margin-top: 30px;
            margin-bottom: 50px;
        }
    </style>
</head>
<body>

    <!-- Header Peach (match lebar .container / stepper) -->
    @if (!request()->routeIs('booking.payment'))
    <header style="background:transparent; padding-top:16px; padding-bottom:0;">
        <div class="container">
            <div style="
                background:#f1cfc4;
                border-radius:18px;
                padding:24px 24px;">
                <h1 style="font-weight:800; font-size:36px; color:#111; margin:0;">
                    PESMA Booking Form
                </h1>
            </div>
        </div>
    </header>
@endif



    <!-- Alert pesan sukses / error -->
    <div class="container mt-3">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Konten utama -->
    <main class="container">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
