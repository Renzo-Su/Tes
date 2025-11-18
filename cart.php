<?php
// cart.php
// JANGAN ada session_start() disini, karena sudah ada di header.php
require_once 'classes/Cart.php';
include_once 'template/header.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$cart = new Cart();
$user_id = $_SESSION['user_id'];

// Logic Hapus / Update Item
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'remove' && isset($_GET['id'])) {
        $cart->removeItem($_GET['id'], $user_id);
        echo "<script>window.location.href='cart.php';</script>";
    }
    if ($_GET['action'] == 'update' && isset($_GET['id'])) {
        $cart->updateQuantity($_GET['id'], $_GET['qty'], $user_id);
        echo "<script>window.location.href='cart.php';</script>";
    }
}

$items = $cart->getCartItems($user_id);
$grand_total = 0;
?>

<div class="container mt-4 mb-5 page-content">
    <h2 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja</h2>

    <?php if (empty($items)): ?>
        <div class="alert alert-warning text-center py-5">
            <h4>Keranjang Anda kosong.</h4>
            <a href="index.php" class="btn btn-primary mt-3">Mulai Belanja</a>
        </div>
    <?php else: ?>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $grand_total += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center p-2">
                                    <img src="assets/images/<?php echo htmlspecialchars($item['cover_image']); ?>" 
                                         style="width: 60px; height: 80px; object-fit: cover;" class="rounded me-3 shadow-sm">
                                    <div>
                                        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($item['title']); ?></h6>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <div class="input-group input-group-sm justify-content-center" style="width: 110px; margin: auto;">
                                    <a href="cart.php?action=update&id=<?php echo $item['cart_id']; ?>&qty=<?php echo $item['quantity'] - 1; ?>" 
                                       class="btn btn-outline-secondary">-</a>
                                    <span class="input-group-text bg-white border-secondary px-3 fw-bold"><?php echo $item['quantity']; ?></span>
                                    <a href="cart.php?action=update&id=<?php echo $item['cart_id']; ?>&qty=<?php echo $item['quantity'] + 1; ?>" 
                                       class="btn btn-outline-secondary">+</a>
                                </div>
                            </td>
                            <td class="text-end fw-bold text-primary">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <a href="cart.php?action=remove&id=<?php echo $item['cart_id']; ?>" 
                                   class="btn btn-danger btn-sm rounded-circle" 
                                   onclick="return confirm('Hapus item ini?');" title="Hapus">
                                   <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Lanjut Belanja
                        </a>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <span class="h4 me-3">Total: <span class="text-danger fw-bold">Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></span></span>
                        <a href="checkout.php" class="btn btn-success btn-lg px-4 shadow">
                            Checkout <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php 
// Ini penutup file cart.php yang benar
include_once 'template/footer.php'; 
?>