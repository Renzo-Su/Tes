<?php
// checkout.php
require_once 'classes/Cart.php';
require_once 'classes/Order.php';
include_once 'template/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>"; exit;
}

$user_id = $_SESSION['user_id'];
$cart = new Cart();
$items = $cart->getCartItems($user_id);

if (empty($items)) {
    echo "<script>window.location.href='index.php';</script>"; exit;
}

$grand_total = 0;
foreach ($items as $item) $grand_total += ($item['price'] * $item['quantity']);

// --- PROSES BAYAR ---
if (isset($_POST['bayar_sekarang'])) {
    $order = new Order();
    
    // Mengambil data dummy dari form (hanya untuk disimpan di logic checkout kalau mau dikembangkan)
    $nama_penerima = $_POST['nama_penerima'];
    $alamat_lengkap = $_POST['alamat_pengiriman'];
    $metode_bayar = $_POST['metode_pembayaran'];
    
    // Gabungkan info untuk disimpan (opsional, tergantung struktur database Order Anda)
    // Disini kita pakai alamat dari form untuk diteruskan ke fungsi checkout
    $alamat_final = "$nama_penerima ($metode_bayar) - $alamat_lengkap";
    
    if ($order->checkout($user_id, $alamat_final)) {
        ?>
        <div class="container mt-5 mb-5 text-center">
            <div class="card shadow-lg border-0 mx-auto p-5" style="max-width: 600px;">
                <div class="mb-4 text-success">
                    <i class="fas fa-check-circle fa-5x animate__animated animate__bounceIn"></i>
                </div>
                <h2 class="text-success fw-bold mb-3">Pesanan Berhasil Dibuat!</h2>
                <p class="text-muted">Terima kasih <b><?php echo htmlspecialchars($nama_penerima); ?></b>, buku Anda akan segera kami kirim ke alamat tujuan.</p>
                <hr>
                <div class="text-start bg-light p-3 rounded mb-4 small">
                    <strong>Metode Bayar:</strong> <?php echo htmlspecialchars($metode_bayar); ?><br>
                    <strong>Total:</strong> Rp <?php echo number_format($grand_total, 0, ',', '.'); ?>
                </div>
                <a href="index.php" class="btn btn-primary w-100">Kembali Belanja</a>
            </div>
        </div>
        <?php
        include_once 'template/footer.php';
        exit;
    } else {
        echo "<script>alert('Gagal memproses pesanan.');</script>";
    }
}
?>

<div class="container mt-4 mb-5 page-content">
    <h2 class="mb-4 fw-bold"><i class="fas fa-credit-card me-2"></i>Checkout & Pembayaran</h2>

    <form method="POST" action="">
        <div class="row">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark">Informasi Pengiriman</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Nama Penerima</label>
                            <input type="text" name="nama_penerima" class="form-control" 
                                   value="<?php echo $_SESSION['username']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon / WhatsApp</label>
                            <input type="number" name="no_telp" class="form-control" placeholder="08xxxxxxxxxx" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat_pengiriman" class="form-control" rows="3" 
                                      placeholder="Nama Jalan, Nomor Rumah, Kecamatan, Kota..." required></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark">Metode Pembayaran</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-check mb-3 border p-3 rounded">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="bca" value="Transfer Bank BCA" checked>
                            <label class="form-check-label w-100 fw-bold" for="bca">
                                <i class="fas fa-university me-2"></i> Transfer Bank BCA
                                <small class="d-block text-muted fw-normal">Dicek Otomatis</small>
                            </label>
                        </div>
                        <div class="form-check mb-3 border p-3 rounded">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="cod" value="COD (Bayar di Tempat)">
                            <label class="form-check-label w-100 fw-bold" for="cod">
                                <i class="fas fa-hand-holding-usd me-2"></i> COD (Bayar di Tempat)
                                <small class="d-block text-muted fw-normal">Bayar tunai ke kurir</small>
                            </label>
                        </div>
                        <div class="form-check border p-3 rounded">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="ewallet" value="GoPay / OVO / Dana">
                            <label class="form-check-label w-100 fw-bold" for="ewallet">
                                <i class="fas fa-wallet me-2"></i> E-Wallet (QRIS)
                                <small class="d-block text-muted fw-normal">Scan QR Code</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow border-0 bg-light">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Ringkasan Pesanan</h5>
                        
                        <div class="mb-3" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($items as $item): ?>
                                <div class="d-flex align-items-center mb-3 bg-white p-2 rounded border">
                                    <img src="assets/images/<?php echo htmlspecialchars($item['cover_image']); ?>" 
                                         style="width: 50px; height: 65px; object-fit: cover;" class="rounded me-2">
                                    <div class="flex-grow-1 lh-sm">
                                        <small class="fw-bold d-block text-truncate" style="max-width: 180px;">
                                            <?php echo htmlspecialchars($item['title']); ?>
                                        </small>
                                        <small class="text-muted"><?php echo $item['quantity']; ?> x Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></small>
                                    </div>
                                    <div class="fw-bold small">
                                        Rp <?php echo number_format($item['price']*$item['quantity'], 0, ',', '.'); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span class="fw-bold">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Ongkos Kirim</span>
                            <span class="text-success fw-bold">Gratis</span>
                        </div>
                        <hr class="border-secondary">
                        <div class="d-flex justify-content-between mb-4 align-items-center">
                            <span class="h6 fw-bold mb-0">Total Bayar</span>
                            <span class="h4 fw-bold text-primary mb-0">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></span>
                        </div>

                        <button type="submit" name="bayar_sekarang" class="btn btn-success w-100 py-3 fw-bold shadow hover-scale">
                            BUAT PESANAN
                        </button>
                        <p class="text-center mt-3 small text-muted">
                            Dengan mengklik tombol di atas, Anda menyetujui Syarat & Ketentuan kami.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include_once 'template/footer.php'; ?>