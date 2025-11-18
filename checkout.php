<?php
// checkout.php
session_start();
// Nyalakan error reporting agar kalau ada salah ketahuan
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'classes/Cart.php';
require_once 'classes/Order.php';
include_once 'template/header.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Login dulu bos!'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = new Cart();
$items = $cart->getCartItems($user_id);

// 2. Cek Keranjang
if (empty($items)) {
    echo "<div class='container mt-5 alert alert-warning'>Keranjang kosong. <a href='index.php'>Belanja dulu yuk!</a></div>";
    include_once 'template/footer.php';
    exit;
}

// Hitung total
$grand_total = 0;
foreach ($items as $item) $grand_total += ($item['price'] * $item['quantity']);

// --- PROSES PEMBAYARAN OTOMATIS ---
if (isset($_POST['konfirmasi_bayar'])) {
    $order = new Order();
    // Kita isi alamat otomatis dengan strip (-) agar tidak error di database
    $alamat_otomatis = "Alamat Default (Langsung di Toko)";
    
    $hasil = $order->checkout($user_id, $alamat_otomatis);
    
    if ($hasil === true) {
        // SUKSES
        echo "<div class='container mt-5 text-center'>
                <div class='alert alert-success py-5'>
                    <h1><i class='fas fa-check-circle'></i></h1>
                    <h3>Pembayaran Berhasil!</h3>
                    <p>Terima kasih sudah berbelanja.</p>
                    <a href='index.php' class='btn btn-primary'>Kembali ke Menu Utama</a>
                </div>
              </div>";
        include_once 'template/footer.php';
        exit;
    } else {
        // GAGAL
        echo "<div class='container mt-3 alert alert-danger'>
                <h4>Gagal Proses:</h4> <p>$hasil</p>
              </div>";
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Konfirmasi Pembayaran</h4>
                </div>
                <div class="card-body">
                    <h5 class="text-center mb-4">Anda akan membeli <b><?php echo count($items); ?></b> item buku.</h5>
                    
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between bg-light">
                            <span>Total Tagihan:</span>
                            <strong class="text-danger fs-4">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></strong>
                        </li>
                    </ul>

                    <form method="POST">
                        <div class="d-grid gap-2">
                            <button type="submit" name="konfirmasi_bayar" class="btn btn-success btn-lg">
                                <i class="fas fa-money-bill-wave"></i> BAYAR SEKARANG
                            </button>
                            <a href="cart.php" class="btn btn-secondary">Batal / Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'template/footer.php'; ?>