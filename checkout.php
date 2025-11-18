<?php
// checkout.php
session_start();
require_once 'classes/Cart.php';
require_once 'classes/Order.php';
include_once 'template/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>"; exit;
}

$user_id = $_SESSION['user_id'];
$cart = new Cart();
$items = $cart->getCartItems($user_id);

// Jika keranjang kosong, lempar balik
if (empty($items)) {
    echo "<script>window.location.href='index.php';</script>"; exit;
}

$grand_total = 0;
foreach ($items as $item) $grand_total += ($item['price'] * $item['quantity']);
$jumlah_item = count($items);

// --- PROSES BAYAR ---
if (isset($_POST['bayar_sekarang'])) {
    $order = new Order();
    $alamat_otomatis = "Alamat Default (Ambil di Toko)";
    
    if ($order->checkout($user_id, $alamat_otomatis)) {
        ?>
        <main class="container mt-5 text-center" style="min-height: 60vh;">
            <div class="card shadow border-success mx-auto" style="max-width: 500px;">
                <div class="card-body py-5">
                    <div class="mb-4 text-success">
                        <i class="fas fa-check-circle fa-5x"></i>
                    </div>
                    <h2 class="text-success fw-bold">Pembayaran Berhasil!</h2>
                    <p class="lead text-muted mt-3">Terima kasih, pesanan Anda sedang diproses.</p>
                    <a href="index.php" class="btn btn-primary btn-lg mt-4 px-5 rounded-pill">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </main>
        <?php
        include_once 'template/footer.php'; exit;
    } else {
        echo "<div class='container mt-5'><div class='alert alert-danger'>Gagal memproses pesanan.</div></div>";
    }
}
?>

<main class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h4 class="mb-0 fw-bold"><i class="fas fa-wallet me-2"></i>Konfirmasi Pembayaran</h4>
                </div>
                <div class="card-body p-4 text-center">
                    
                    <p class="text-muted mb-2">Total Tagihan Untuk</p>
                    <h3 class="fw-bold text-dark mb-4"><?php echo $jumlah_item; ?> Jenis Buku</h3>

                    <div class="bg-light border rounded p-3 mb-4">
                        <h2 class="text-danger fw-bold m-0">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></h2>
                    </div>

                    <form method="POST">
                        <div class="d-grid gap-3">
                            <button type="submit" name="bayar_sekarang" class="btn btn-success btn-lg py-3 fw-bold shadow-sm">
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
</main>

<?php include_once 'template/footer.php'; ?>