<?php
// cart.php
require_once 'classes/Cart.php';
include_once 'template/header.php'; // Header otomatis start session

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.php';</script>";
    exit;
}

$cart = new Cart();
$user_id = $_SESSION['user_id'];

// --- LOGIKA HAPUS ITEM ---
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $cart_id = $_GET['id'];
    if ($cart->removeItem($cart_id, $user_id)) {
        echo "<div class='alert alert-success container mt-3'>Item berhasil dihapus.</div>";
    }
}

// Ambil data keranjang
$items = $cart->getCartItems($user_id);
$grand_total = 0;
?>

<div class="container mt-4">
    <h2><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h2>
    <hr>

    <?php if (empty($items)): ?>
        <div class="alert alert-warning text-center">
            <h4>Keranjang kosong.</h4>
            <a href="index.php" class="btn btn-primary mt-2">Mulai Belanja</a>
        </div>
    <?php else: ?>
        
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Gambar</th>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                            <?php 
                            // LOGIKA UPDATE QUANTITY (Taruh di bagian paling atas file cart.php sebenarnya, tapi disini juga oke)
                            if (isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['id']) && isset($_GET['qty'])) {
                                $cart->updateQuantity($_GET['id'], $_GET['qty'], $user_id);
                                // Redirect agar URL bersih
                                echo "<script>window.location.href='cart.php';</script>";
                            }
                            
                            $no = 1; 
                            foreach ($items as $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                                $grand_total += $subtotal;
                            ?>
                            <tr>
                                <td class="text-center">
                                    <img src="assets/images/<?php echo htmlspecialchars($item['cover_image']); ?>" width="60">
                                </td>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td class="text-end">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                <td class="text-center" style="width: 150px;">
                                    
                                    <div class="input-group input-group-sm justify-content-center">
                                        <a href="cart.php?action=update&id=<?php echo $item['cart_id']; ?>&qty=<?php echo $item['quantity'] - 1; ?>" 
                                           class="btn btn-outline-secondary <?php echo ($item['quantity'] <= 1) ? 'disabled' : ''; ?>">
                                           -
                                        </a>
                                        
                                        <span class="input-group-text bg-white px-3"><?php echo $item['quantity']; ?></span>
                                        
                                        <a href="cart.php?action=update&id=<?php echo $item['cart_id']; ?>&qty=<?php echo $item['quantity'] + 1; ?>" 
                                           class="btn btn-outline-secondary">
                                           +
                                        </a>
                                    </div>

                                </td>
                                <td class="text-end fw-bold">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <a href="cart.php?action=remove&id=<?php echo $item['cart_id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Hapus?');">
                                       <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="4" class="text-end fw-bold fs-5">Total Bayar:</td>
                        <td class="text-end fw-bold fs-5 text-danger">
                            Rp <?php echo number_format($grand_total, 0, ',', '.'); ?>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mb-5">
            <a href="index.php" class="btn btn-secondary">&laquo; Lanjut Belanja</a>
            <a href="checkout.php" class="btn btn-success btn-lg">Checkout &raquo;</a>
        </div>

    <?php endif; ?>
</div>

<?php include_once 'template/footer.php'; ?>