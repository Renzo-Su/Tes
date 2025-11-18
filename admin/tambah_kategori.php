<?php
// admin/tambah_kategori.php
require_once 'auth_guard.php';
require_once '../classes/Category.php';

$message = "";

if (isset($_POST['submit_category'])) {
    $cat_name = $_POST['category_name'];
    
    if (!empty($cat_name)) {
        $category = new Category();
        if ($category->create($cat_name)) {
            // Jika sukses, user bisa pilih mau nambah lagi atau balik ke tambah buku
            $message = '<div class="alert alert-success">Kategori <b>'.htmlspecialchars($cat_name).'</b> berhasil dibuat! <a href="tambah_buku.php">Kembali ke Tambah Buku</a></div>';
        } else {
            $message = '<div class="alert alert-danger">Gagal! Kategori mungkin sudah ada.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Nama kategori tidak boleh kosong.</div>';
    }
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Tambah Kategori Baru</h5>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        
                        <form action="tambah_kategori.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nama Kategori</label>
                                <input type="text" name="category_name" class="form-control" placeholder="Misal: Komik, Sains..." required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="tambah_buku.php" class="btn btn-outline-secondary">Batal / Kembali</a>
                                <button type="submit" name="submit_category" class="btn btn-success">Simpan Kategori</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>