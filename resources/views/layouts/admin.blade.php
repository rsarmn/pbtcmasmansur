<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Pesma Inn</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background: #f7f7f7;
        }
        .admin-header {
            background: #a0203c;
            padding: 15px;
            color: white;
            font-size: 20px;
            font-weight: bold;
        }
        .sidebar {
            background: white;
            padding: 20px;
            min-height: 100vh;
            border-right: 1px solid #ddd;
        }
        .sidebar a {
            display: block;
            padding: 10px 0;
            color: #a0203c;
            font-weight: 600;
            text-decoration: none;
        }
        .sidebar a:hover {
            text-decoration: underline;
        }
        .content {
            padding: 25px;
        }
    </style>
</head>
<body>

<div class="admin-header">
    Admin • Pesma Inn KH. Mas Mansur
</div>

<div class="container-fluid">
    <div class="row">

        {{-- Sidebar --}}
        <div class="col-md-2 sidebar">
            <a href="{{ url('/admin/booking') }}"> Booking List</a>
            <a href="{{ url('/beranda') }}"> Edit Beranda</a>
            <a href="{{ url('/') }}"> View Website</a>
        </div>

        {{-- Content --}}
        <div class="col-md-10 content">
            @yield('content')
        </div>

    </div>
</div>

</body>
</html>
