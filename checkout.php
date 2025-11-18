<?php
// checkout.php
// NYALAKAN DEBUGGING UNTUK MELIHAT ERROR
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'classes/Cart.php';
require_once 'classes/Order.php';
include_once 'template/header.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = new Cart();
$items = $cart->getCartItems($user_id);

// 2. Cek Keranjang
if (empty($items)) {
    echo "<div class='container mt-5 alert alert-warning'>Keranjang kosong. <a href='index.php'>Belanja dulu</a></div>";
    include_once 'template/footer.php';
    exit;
}

// Hitung total
$grand_total = 0;
foreach ($items as $item) $grand_total += ($item['price'] * $item['quantity']);

// PROSES FORM
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bayar'])) {
    $alamat = trim($_POST['alamat']);
    
    if (empty($alamat)) {
        echo "<script>alert('Alamat tidak boleh kosong!');</script>";
    } else {
        $order = new Order();
        
        // Simpan hasil checkout ke variabel
        $hasil = $order->checkout($user_id, $alamat);
        
        // Cek apakah hasilnya TRUE (Sukses) atau String (Error)
        if ($hasil === true) {
            // SUKSES
            echo "<div class='container mt-5 text-center'>
                    <div class='alert alert-success py-5'>
                        <h1><i class='fas fa-check-circle'></i></h1>
                        <h3>Pesanan Berhasil!</h3>
                        <p>Data pesanan sudah masuk database.</p>
                        <a href='index.php' class='btn btn-primary'>Kembali ke Beranda</a>
                    </div>
                  </div>";
            include_once 'template/footer.php';
            exit;
        } else {
            // GAGAL - Tampilkan error spesifik dari database
            echo "<div class='container mt-4'>
                    <div class='alert alert-danger'>
                        <h4><i class='fas fa-exclamation-triangle'></i> Checkout Gagal!</h4>
                        <p>Sistem menolak pesanan dengan pesan error berikut:</p>
                        <pre class='bg-white p-3 border'>$hasil</pre>
                        <p>Tolong screenshot error di atas dan cek Database Anda.</p>
                    </div>
                  </div>";
        }
    }
}
?>

<div class="container mt-4 mb-5">
    <h2>Checkout</h2>
    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Alamat Pengiriman</div>
                <div class="card-body">
                    <form method="POST" action="checkout.php">
                        <div class="mb-3">
                            <label>Nama Penerima</label>
                            <input type="text" class="form-control" value="<?php echo $_SESSION['username']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3" required placeholder="Jalan, Kota, Kode Pos..."></textarea>
                        </div>
                        <button type="submit" name="bayar" class="btn btn-success w-100 btn-lg">
                            <i class="fas fa-money-bill-wave"></i> Buat Pesanan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="card bg-light">
                <div class="card-body">
                    <h5>Ringkasan (<?php echo count($items); ?> item)</h5>
                    <hr>
                    <ul class="list-group mb-3">
                        <?php foreach($items as $item): ?>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0"><?php echo $item['title']; ?></h6>
                                <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                            </div>
                            <span class="text-muted">Rp <?php echo number_format($item['price'] * $item['quantity'], 0,',','.'); ?></span>
                        </li>
                        <?php endforeach; ?>
                        <li class="list-group-item d-flex justify-content-between bg-white">
                            <span>Total (IDR)</span>
                            <strong class="text-danger">Rp <?php echo number_format($grand_total, 0,',','.'); ?></strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'template/footer.php'; ?>