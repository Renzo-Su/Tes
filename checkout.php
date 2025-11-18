<?php
// checkout.php
require_once 'classes/Cart.php';
require_once 'classes/Order.php';
include_once 'template/header.php'; // Header handles session_start

// Cek Login
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>"; exit;
}

$user_id = $_SESSION['user_id'];
$cart = new Cart();
$items = $cart->getCartItems($user_id);

// Jika kosong, lempar balik
if (empty($items)) {
    echo "<script>window.location.href='index.php';</script>"; exit;
}

$grand_total = 0;
foreach ($items as $item) $grand_total += ($item['price'] * $item['quantity']);
$jumlah_item = count($items);

// --- PROSES KLIK BAYAR ---
if (isset($_POST['bayar_sekarang'])) {
    $order = new Order();
    $alamat_otomatis = "Alamat Default (Ambil di Toko)";
    
    if ($order->checkout($user_id, $alamat_otomatis)) {
        // SUKSES
        ?>
        <div class="container mt-5 text-center page-content">
            <div class="card shadow border-success mx-auto" style="max-width: 500px;">
                <div class="card-body py-5">
                    <h1 class="text-success display-1 mb-3"><i class="fas fa-check-circle"></i></h1>
                    <h2 class="text-success">Pembayaran Berhasil!</h2>
                    <p class="lead mb-4">Terima kasih telah berbelanja.</p>
                    <a href="index.php" class="btn btn-primary btn-lg">Kembali ke Menu Utama</a>
                </div>
            </div>
        </div>
        <?php
        include_once 'template/footer.php';
        exit;
    } else {
        // GAGAL
        echo "<div class='container mt-5'><div class='alert alert-danger'>Gagal memproses pesanan.</div></div>";
    }
}
?>

<div class="container mt-5 mb-5 page-content">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0"><i class="fas fa-wallet me-2"></i>Konfirmasi Pembayaran</h4>
                </div>
                <div class="card-body p-4 text-center">
                    
                    <p class="text-muted mb-1">Anda akan melakukan checkout untuk:</p>
                    <h3 class="fw-bold text-dark mb-4"><?php echo $jumlah_item; ?> Jenis Buku</h3>

                    <div class="alert alert-light border d-flex justify-content-between align-items-center px-4">
                        <span class="h5 mb-0">Total Tagihan:</span>
                        <span class="h3 mb-0 text-danger fw-bold">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></span>
                    </div>

                    <form method="POST" class="mt-4">
                        <div class="d-grid gap-3">
                            <button type="submit" name="bayar_sekarang" class="btn btn-success btn-lg py-3 shadow-sm">
                                <i class="fas fa-money-bill-wave me-2"></i> BAYAR SEKARANG
                            </button>
                            
                            <a href="cart.php" class="btn btn-outline-secondary">
                                Batal / Kembali
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'template/footer.php'; ?>