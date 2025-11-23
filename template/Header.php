<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Toko Buku Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>body { display: flex; flex-direction: column; min-height: 100vh; background-color: #f8f9fa; } .page-content { flex: 1; } </style>
  </head>
  <body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-0">
      <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-book-open me-2"></i>TokoBuku</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                  <a class="nav-link position-relative" href="cart.php">
                    <i class="fas fa-shopping-cart"></i> Keranjang
                  </a>
                </li>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <li class="nav-item"><a class="btn btn-danger btn-sm ms-2" href="admin/index.php">Panel Admin</a></li>
                <?php endif; ?>

                <li class="nav-item dropdown ms-2">
                  <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">
                    Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="orders.php"><i class="fas fa-history me-2"></i> Riwayat Pesanan</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                  </ul>
                </li>

            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <li class="nav-item"><a class="btn btn-primary btn-sm ms-2" href="register.php">Daftar</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>