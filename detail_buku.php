<?php
// detail_buku.php
require_once 'classes/Book.php'; // Panggil class Book
require_once 'classes/Category.php'; // Kita perlu ini untuk ambil nama kategori

// Panggil header
include_once 'template/header.php';

// --- Logika Ambil Data ---

// 1. Cek apakah ada ID di URL (misal: detail_buku.php?id=5)
if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>Error: ID Buku tidak ditemukan.</div>";
    include_once 'template/footer.php';
    exit;
}

$book_id = $_GET['id'];
$book = new Book();
$book_data = $book->getById($book_id);

// 2. Cek jika buku dengan ID tsb tidak ada di database
if (!$book_data) {
    echo "<div class='alert alert-danger'>Error: Buku tidak ditemukan.</div>";
    include_once 'template/footer.php';
    exit;
}

// 3. (Opsional) Ambil nama kategori berdasarkan category_id
$category = new Category();
$categories = $category->getAll(); // Ambil semua
$category_name = "N/A"; // Nama default
foreach ($categories as $cat) {
    if ($cat['id'] == $book_data['category_id']) {
        $category_name = $cat['category_name'];
        break;
    }
}
?>

<div class="container">
    <a href="index.php" class="btn btn-link mb-2">&laquo; Kembali ke katalog</a>
    <div class="row">
        <div class="col-md-4">
            <img src="assets/images/<?php echo htmlspecialchars($book_data['cover_image']); ?>" 
                 class="img-fluid rounded shadow-sm w-100" 
                 alt="<?php echo htmlspecialchars($book_data['title']); ?>">
        </div>

        <div class="col-md-8">
            <h2><?php echo htmlspecialchars($book_data['title']); ?></h2>
            <h5 class="text-muted"><?php echo htmlspecialchars($book_data['author']); ?></h5>
            
            <span class="badge bg-secondary mb-3 fs-6"><?php echo htmlspecialchars($category_name); ?></span>
            
            <h3 class="text-danger my-3">
                Rp <?php echo number_format($book_data['price'], 0, ',', '.'); ?>
            </h3>

            <p><strong>Stok Tersedia:</strong> <?php echo htmlspecialchars($book_data['stock']); ?> buah</p>

            <hr>
            
            <h4>Deskripsi</h4>
            <p><?php echo nl2br(htmlspecialchars($book_data['description'])); ?></p>
            
            <hr>

            <<div class="d-flex">
                <input type="number" class="form-control me-3" id="quantity" value="1" min="1" max="<?php echo htmlspecialchars($book_data['stock']); ?>" style="width: 100px;">
                <button class="btn btn-success btn-lg btn-add-to-cart"
                        data-book-id="<?php echo $book_data['id']; ?>">
                    Tambah ke Keranjang
                </button>
            </div>
        </div>
    </div>
</div>

<?php
// Panggil footer
include_once 'template/footer.php';
?>