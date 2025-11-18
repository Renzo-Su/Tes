<?php
// admin/index.php
require_once 'auth_guard.php'; // Pastikan user adalah admin
require_once '../classes/Book.php'; 

$book = new Book(); 
$books = $book->getAll(); // Ambil data buku

// LOGIKA DELETE
$message = "";
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $book_id = $_GET['id'];
    if ($book->delete($book_id)) {
        $message = '<div class="alert alert-success">Buku berhasil dihapus.</div>';
        // Refresh data agar tabel terupdate
        $books = $book->getAll(); 
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
    <title>Admin Panel - TokoBuku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-danger mb-4">
      <div class="container">
        <a class="navbar-brand" href="index.php">ADMIN PANEL</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="../index.php" target="_blank">Lihat Website</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-bold" href="../logout.php">LOGOUT</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container">
        <h2>Manajemen Buku</h2>
        <p>Halo Admin, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>!</p>

        <a href="tambah_buku.php" class="btn btn-primary mb-3">+ Tambah Buku Baru</a>

        <?php echo $message; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No.</th>
                        <th>Cover</th>
                        <th>Judul Buku</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($books)): ?>
                        <tr><td colspan="7" class="text-center">Belum ada data buku.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($books as $b): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <img src="../assets/images/<?php echo htmlspecialchars($b['cover_image']); ?>" width="50">
                            </td>
                            <td><?php echo htmlspecialchars($b['title']); ?></td>
                            <td><?php echo htmlspecialchars($b['category_name'] ?? '-'); ?></td>
                            <td>Rp <?php echo number_format($b['price'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($b['stock']); ?></td>
                            <td>
                                <a href="edit_buku.php?id=<?php echo $b['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="index.php?action=delete&id=<?php echo $b['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin hapus?');">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>