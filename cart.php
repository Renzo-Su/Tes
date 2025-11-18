<?php
// cart.php
require_once 'classes/Cart.php';
include_once 'template/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>"; exit;
}

$cart = new Cart();
$user_id = $_SESSION['user_id'];

// Logic Update/Hapus
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'remove' && isset($_GET['id'])) {
        $cart->removeItem($_GET['id'], $user_id);
        echo "<script>window.location.href='cart.php';</script>";
    }
    // Validasi server side agar tidak bisa minus
    if ($_GET['action'] == 'update' && isset($_GET['id'])) {
        $new_qty = (int)$_GET['qty'];
        if ($new_qty >= 1) {
             $cart->updateQuantity($_GET['id'], $new_qty, $user_id);
        }
        echo "<script>window.location.href='cart.php';</script>";
    }
}

$items = $cart->getCartItems($user_id);
$grand_total = 0;
?>

<div class="container mt-4 mb-5 page-content">
    <h2 class="mb-4 fw-bold text-dark">Keranjang Belanja</h2>

    <?php if (empty($items)): ?>
        <div class="text-center py-5 bg-light rounded shadow-sm">
            <img src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" alt="Empty Cart" style="width: 100px; opacity: 0.5;">
            <h4 class="mt-3 text-muted">Wah, keranjangmu masih kosong.</h4>
            <a href="index.php" class="btn btn-primary mt-3 px-4 rounded-pill">Mulai Belanja Sekarang</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white py-3">
                        <span class="fw-bold text-secondary">Daftar Item</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light text-secondary small">
                                <tr>
                                    <th class="ps-4">Produk</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end pe-4">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): 
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $grand_total += $subtotal;
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/<?php echo htmlspecialchars($item['cover_image']); ?>" 
                                                 style="width: 60px; height: 80px; object-fit: cover;" class="rounded me-3 border">
                                            <div>
                                                <h6 class="mb-1 text-dark fw-semibold"><?php echo htmlspecialchars($item['title']); ?></h6>
                                                <small class="text-muted">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm justify-content-center" style="width: 110px; margin: 0 auto;">
                                            <?php if($item['quantity'] > 1): ?>
                                                <a href="cart.php?action=update&id=<?php echo $item['cart_id']; ?>&qty=<?php echo $item['quantity'] - 1; ?>" 
                                                   class="btn btn-outline-secondary">-</a>
                                            <?php else: ?>
                                                <button class="btn btn-outline-secondary" disabled>-</button>
                                            <?php endif; ?>
                                            
                                            <input type="text" class="form-control text-center bg-white" value="<?php echo $item['quantity']; ?>" readonly>
                                            
                                            <a href="cart.php?action=update&id=<?php echo $item['cart_id']; ?>&qty=<?php echo $item['quantity'] + 1; ?>" 
                                               class="btn btn-outline-secondary">+</a>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4 fw-bold">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <a href="cart.php?action=remove&id=<?php echo $item['cart_id']; ?>" 
                                           class="text-danger" 
                                           onclick="return confirm('Hapus item ini?');">
                                           <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">Total Keranjang</h5>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 fw-bold">Total</span>
                            <span class="h5 fw-bold text-primary">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></span>
                        </div>
                        <a href="checkout.php" class="btn btn-dark w-100 py-3 fw-bold shadow-sm">
                            Lanjut ke Pembayaran <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

