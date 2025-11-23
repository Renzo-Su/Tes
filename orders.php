<?php
// orders.php - Riwayat Pesanan User
require_once 'classes/Database.php';
include_once 'template/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>"; exit;
}

$user_id = $_SESSION['user_id'];
$db = new Database();
$conn = $db->getConnection();

// Ambil data orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY order_date DESC");
$stmt->execute([':uid' => $user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5 mb-5 page-content">
    <h2 class="mb-4 fw-bold"><i class="fas fa-history me-2"></i>Riwayat Pesanan Saya</h2>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info text-center py-5">
            <h4>Belum ada riwayat pesanan.</h4>
            <a href="index.php" class="btn btn-primary mt-3">Mulai Belanja</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($orders as $order): ?>
                <div class="col-md-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <div>
                                <span class="fw-bold">Order #<?php echo $order['id']; ?></span>
                                <span class="text-muted small ms-2"><?php echo date('d M Y H:i', strtotime($order['order_date'])); ?></span>
                            </div>
                            <?php 
                                $statusClass = 'bg-warning text-dark';
                                if($order['status'] == 'paid' || $order['status'] == 'shipped') $statusClass = 'bg-success';
                                if($order['status'] == 'cancelled') $statusClass = 'bg-danger';
                            ?>
                            <span class="badge <?php echo $statusClass; ?> rounded-pill px-3">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <?php 
                            $stmtDetail = $conn->prepare("
                                SELECT od.*, b.title, b.cover_image 
                                FROM order_details od 
                                JOIN books b ON od.book_id = b.id 
                                WHERE od.order_id = :oid
                            ");
                            $stmtDetail->execute([':oid' => $order['id']]);
                            $details = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            
                            <div class="table-responsive">
                                <table class="table table-borderless table-sm mb-0">
                                    <?php foreach($details as $d): ?>
                                    <tr>
                                        <td style="width: 60px;">
                                            <img src="assets/images/<?php echo htmlspecialchars($d['cover_image']); ?>" width="50" class="rounded">
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($d['title']); ?></div>
                                            <small class="text-muted"><?php echo $d['quantity']; ?> x Rp <?php echo number_format($d['price_at_purchase']); ?></small>
                                        </td>
                                        <td class="text-end fw-bold">
                                            Rp <?php echo number_format($d['quantity'] * $d['price_at_purchase']); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Dikirim ke: <?php echo htmlspecialchars($order['shipping_address']); ?></small>
                                <h5 class="mb-0 fw-bold text-primary">Total: Rp <?php echo number_format($order['total_amount']); ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include_once 'template/footer.php'; ?>