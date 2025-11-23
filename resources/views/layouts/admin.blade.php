<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Pesma Inn</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

<div class="admin-header d-flex justify-content-between align-items-center">
    <div>Admin • Pesma Inn KH. Mas Mansur</div>
    <form method="POST" action="{{ route('auth.logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-light btn-sm">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </form>
</div>

<div class="container-fluid">
    <div class="row">

        {{-- Sidebar --}}
        <div class="col-md-2 sidebar">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('kamar.index') }}">Data Kamar</a>
            <a href="{{ route('pengunjung.index') }}">Data Pengunjung</a>
            <a href="{{ route('pembayaran.konfirmasi') }}">Pembayaran</a>
            <a href="{{ route('report.monthly') }}">Report</a>
            <a href="{{ route('beranda.edit') }}">Edit Beranda</a>
            <hr>
            <a href="{{ route('beranda.show') }}" target="_blank">🌐 View Website</a>
        </div>

        {{-- Content --}}
        <div class="col-md-10 content">
            @yield('content')
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  @if(session('success'))
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: '{{ session('success') }}',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    });
  @endif

  @if(session('error'))
    Swal.fire({
      icon: 'error',
      title: 'Gagal!',
      text: '{{ session('error') }}',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 4000,
      timerProgressBar: true
    });
  @endif

  @if($errors->any())
    Swal.fire({
      icon: 'error',
      title: 'Validation Error',
      html: '<ul style="text-align:left;margin:0;padding-left:20px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 5000,
      timerProgressBar: true
    });
  @endif
});
</script>

</body>
</html>
