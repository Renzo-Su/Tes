<?php
// admin/edit_buku.php
require_once 'auth_guard.php'; // Panggil penjaga
require_once '../classes/Book.php';
require_once '../classes/Category.php';

$message = "";

// Cek apakah ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: index.php"); // Jika tidak ada ID, tendang
    exit;
}

$book_id = $_GET['id'];
$book = new Book();

// Ambil data buku yang mau di-edit
$book_data = $book->getById($book_id);
if (!$book_data) {
    header("Location: index.php"); // Jika ID tidak ditemukan, tendang
    exit;
}

// Ambil data kategori untuk dropdown
$category = new Category();
$categories = $category->getAll();

// --- LOGIKA UPDATE ---
// Cek jika form disubmit
if (isset($_POST['update'])) {
    
    // Panggil method update
    // $_POST berisi data teks, $_FILES berisi data file
    if ($book->update($_POST, $_FILES['cover_image'])) {
        // Jika berhasil, redirect kembali ke halaman utama admin
        header("Location: index.php?status=update_success");
        exit;
    } else {
        $message = '<div class="alert alert-danger">Gagal mengupdate buku.</div>';
        // Refresh data buku jika gagal (opsional, tapi bagus)
        $book_data = $book->getById($book_id);
    }
}
// --- Selesai Logika Update ---

include_once '../template/header.php';
?>

<h2>Edit Buku</h2>
<a href="index.php" class="btn btn-secondary mb-3">&laquo; Kembali ke Daftar Buku</a>

<?php echo $message; // Tampilkan pesan error (jika ada) ?>

<form action="edit_buku.php?id=<?php echo $book_id; ?>" method="POST" enctype="multipart/form-data">
    
    <input type="hidden" name="id" value="<?php echo $book_data['id']; ?>">
    <input type="hidden" name="old_cover_image" value="<?php echo $book_data['cover_image']; ?>">
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Buku</label>
                        <input type="text" class="form-control" id="title" name="title" required 
                               value="<?php echo htmlspecialchars($book_data['title']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" class="form-control" id="author" name="author" required
                               value="<?php echo htmlspecialchars($book_data['author']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($book_data['description']); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="price" class="form-label">Harga (Rp)</label>
                        <input type="number" class="form-control" id="price" name="price" required min="0"
                               value="<?php echo htmlspecialchars($book_data['price']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stock" name="stock" required min="0"
                               value="<?php echo htmlspecialchars($book_data['stock']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                    <?php if ($cat['id'] == $book_data['category_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cover Saat Ini</label>
                        <img src="../assets/images/<?php echo htmlspecialchars($book_data['cover_image']); ?>" class="img-thumbnail mb-2" width="150">
                        
                        <label for="cover_image" class="form-label">Ganti Gambar Sampul</label>
                        <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/png, image/jpeg, image/jpg">
                        <small class="form-text">Kosongkan jika tidak ingin ganti gambar.</small>
                    </div>

                    <button type="submit" name="update" class="btn btn-primary w-100">Update Buku</button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
include_once '../template/footer.php';
?>