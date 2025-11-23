<?php
require_once 'auth_guard.php'; 
require_once '../classes/Book.php'; 
require_once '../classes/Database.php'; // Butuh koneksi manual utk statistik

$book = new Book(); 
$books = $book->getAll(); 

// Koneksi untuk hitung statistik
$db = new Database();
$conn = $db->getConnection();

// Hitung Total Buku
$stmt1 = $conn->query("SELECT COUNT(*) FROM books");
$total_buku = $stmt1->fetchColumn();

// Hitung Total Pesanan
$stmt2 = $conn->query("SELECT COUNT(*) FROM orders");
$total_order = $stmt2->fetchColumn();

// Hitung Total Pendapatan (Hanya yang tidak cancelled)
$stmt3 = $conn->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled'");
$total_income = $stmt3->fetchColumn();

// Logika Delete
$message = "";
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    if ($book->delete($_GET['id'])) {
        header("Location: index.php"); exit; // Refresh agar bersih
    } else {
        $message = '<div class="alert alert-danger">Gagal menghapus buku.</div>';
    }
}
?>

<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  </head>
  <body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-danger mb-4 shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-user-shield me-2"></i>ADMIN PANEL</a>
        <div class="d-flex">
            <a class="btn btn-outline-light btn-sm me-2" href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Lihat Web</a>
            <a class="btn btn-light btn-sm fw-bold text-danger" href="../logout.php">LOGOUT</a>
        </div>
      </div>
    </nav>

    <div class="container">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-book me-2"></i>Total Buku</h5>
                        <h2 class="fw-bold"><?php echo $total_buku; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-shopping-bag me-2"></i>Total Pesanan</h5>
                        <h2 class="fw-bold"><?php echo $total_order; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3 shadow-sm h-100">
                    <div class="card-body text-dark">
                        <h5 class="card-title"><i class="fas fa-money-bill-wave me-2"></i>Estimasi Pendapatan</h5>
                        <h2 class="fw-bold">Rp <?php echo number_format($total_income, 0, ',', '.'); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0 text-secondary">Daftar Buku</h4>
            <a href="tambah_buku.php" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Tambah Buku</a>
        </div>

        <?php echo $message; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Cover</th>
                                <th>Judul Buku</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($books)): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data buku.</td></tr>
                            <?php else: ?>
                                <?php foreach ($books as $b): ?>
                                <tr>
                                    <td class="ps-3">
                                        <img src="../assets/images/<?php echo htmlspecialchars($b['cover_image']); ?>" width="50" class="rounded border">
                                    </td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($b['title']); ?></td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary border"><?php echo htmlspecialchars($b['category_name'] ?? '-'); ?></span></td>
                                    <td>Rp <?php echo number_format($b['price'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php if($b['stock'] > 5): ?>
                                            <span class="badge bg-success"><?php echo $b['stock']; ?></span>
                                        <?php elseif($b['stock'] > 0): ?>
                                            <span class="badge bg-warning text-dark"><?php echo $b['stock']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Habis</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="edit_buku.php?id=<?php echo $b['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <a href="index.php?action=delete&id=<?php echo $b['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin hapus? Data tidak bisa kembali.');"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>