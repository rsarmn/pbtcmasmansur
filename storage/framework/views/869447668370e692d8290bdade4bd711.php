<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesma Booking System</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    <?php if(!request()->routeIs('booking.payment')): ?>
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
<?php endif; ?>



    <!-- Konten utama -->
    <main class="container">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert Toast Notifications -->
    <script>
        <?php if(session('success')): ?>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            Toast.fire({
                icon: 'success',
                title: '<?php echo e(session("success")); ?>'
            });
        <?php endif; ?>

        <?php if($errors->any()): ?>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                Toast.fire({
                    icon: 'error',
                    title: '<?php echo e($error); ?>'
                });
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\peng\resources\views/layouts/app.blade.php ENDPATH**/ ?>