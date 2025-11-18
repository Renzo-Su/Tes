<?php
// detail_buku.php
require_once 'classes/Book.php'; 
require_once 'classes/Category.php'; 
include_once 'template/header.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='index.php';</script>"; exit;
}

$book_id = $_GET['id'];
$book = new Book();
$book_data = $book->getById($book_id);

if (!$book_data) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Buku tidak ditemukan.</div></div>";
    include_once 'template/footer.php'; exit;
}

// Ambil kategori
$category = new Category();
$categories = $category->getAll();
$category_name = "Umum";
foreach ($categories as $cat) {
    if ($cat['id'] == $book_data['category_id']) {
        $category_name = $cat['category_name'];
        break;
    }
}
?>

<div class="container mt-4 mb-5 page-content">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail Buku</li>
        </ol>
    </nav>

    <div class="card shadow-lg border-0 overflow-hidden">
        <div class="row g-0">
            <div class="col-md-5 bg-light text-center p-4 d-flex align-items-center justify-content-center">
                <img src="assets/images/<?php echo htmlspecialchars($book_data['cover_image']); ?>" 
                     class="img-fluid rounded shadow" 
                     style="max-height: 450px; object-fit: contain;"
                     alt="<?php echo htmlspecialchars($book_data['title']); ?>">
            </div>

            <div class="col-md-7">
                <div class="card-body p-4 p-lg-5">
                    <span class="badge bg-primary bg-opacity-10 text-primary mb-2 px-3 py-2 rounded-pill">
                        <?php echo htmlspecialchars($category_name); ?>
                    </span>
                    
                    <h1 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($book_data['title']); ?></h1>
                    <h5 class="text-muted mb-4">karya <span class="text-dark"><?php echo htmlspecialchars($book_data['author']); ?></span></h5>
                    
                    <h2 class="text-danger fw-bold mb-3">
                        Rp <?php echo number_format($book_data['price'], 0, ',', '.'); ?>
                    </h2>

                    <p class="text-muted mb-4" style="line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars($book_data['description'])); ?>
                    </p>
                    
                    <div class="d-flex align-items-center mb-4">
                        <i class="fas fa-box-open text-secondary me-2"></i>
                        <?php if ($book_data['stock'] > 0): ?>
                            <span class="text-success fw-bold">Stok tersedia: <?php echo htmlspecialchars($book_data['stock']); ?></span>
                        <?php else: ?>
                            <span class="text-danger fw-bold">Stok Habis</span>
                        <?php endif; ?>
                    </div>

                    <hr class="my-4">

                    <?php if ($book_data['stock'] > 0): ?>
                        <div class="d-flex align-items-end gap-3">
                            <div style="width: 100px;">
                                <label for="quantity" class="form-label fw-bold small text-uppercase">Jumlah</label>
                                <input type="number" class="form-control form-control-lg text-center border-secondary" 
                                       id="quantity" value="1" min="1" 
                                       max="<?php echo htmlspecialchars($book_data['stock']); ?>">
                            </div>
                            <div class="flex-grow-1">
                                <button class="btn btn-success btn-lg w-100 py-2 btn-add-to-cart shadow-sm"
                                        data-book-id="<?php echo $book_data['id']; ?>">
                                    <i class="fas fa-cart-plus me-2"></i> Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>Maaf, buku ini sedang tidak tersedia.</div>
                        </div>
                        <button class="btn btn-secondary btn-lg w-100 py-2" disabled>
                            <i class="fas fa-ban me-2"></i> Stok Habis
                        </button>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'template/footer.php'; ?>