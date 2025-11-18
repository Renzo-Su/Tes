<?php
// index.php (Halaman Utama)
require_once 'classes/Book.php'; // Panggil class Book

// Panggil header (header.php sudah otomatis panggil session_start())
include_once 'template/header.php';

$book = new Book(); // Buat objek book
$books = $book->getAll(); // Ambil semua data buku
?>

<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Selamat Datang di Toko Buku Online</h2>
            
            <?php if (isset($_SESSION['username'])): ?>
                <p>Halo, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>! Selamat berbelanja.</p>
            <?php else: ?>
                <p>Silakan <a href="login.php">login</a> atau <a href="register.php">daftar</a> untuk mulai berbelanja.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3 class="mb-3">Katalog Buku</h3>
            <hr>
        </div>
    </div>

    <div class="row">
        <?php if (empty($books)): ?>
            <div class="col-md-12">
                <div class="alert alert-info">Belum ada buku yang tersedia saat ini.</div>
            </div>
        <?php else: ?>
            <?php foreach ($books as $b): ?>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="assets/images/<?php echo htmlspecialchars($b['cover_image']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($b['title']); ?>"
                             style="height: 300px; object-fit: cover;">
                        
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($b['title']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($b['author']); ?></p>
                            <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($b['category_name'] ?? 'N/A'); ?></span>
                            
                            <h4 class="card-text text-danger">
                                Rp <?php echo number_format($b['price'], 0, ',', '.'); ?>
                            </h4>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="detail_buku.php?id=<?php echo $b['id']; ?>" class="btn btn-primary w-100 mb-2">Lihat Detail</a>
                            
                           <button class="btn btn-success w-100 btn-add-to-cart" 
                                data-book-id="<?php echo $b['id']; ?>">
                                    Tambah ke Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
// Panggil footer
include_once 'template/footer.php';
?>