<?php
// classes/Order.php
require_once 'Database.php';
require_once 'Cart.php';

class Order {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Method untuk memindahkan data dari Cart ke Orders
    public function checkout($user_id, $address) {
        try {
            $this->conn->beginTransaction();

            // Cek keranjang
            $cart = new Cart();
            $cart_items = $cart->getCartItems($user_id);

            if (empty($cart_items)) {
                throw new Exception("Keranjang belanja kosong.");
            }

            // Hitung total
            $total = 0;
            foreach ($cart_items as $item) {
                $total += ($item['price'] * $item['quantity']);
            }

            // INSERT ORDERS
            // PASTIKAN nama tabel 'orders' dan kolomnya sesuai di database Anda!
            $query_order = "INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (:uid, :total, :addr, 'pending')";
            $stmt = $this->conn->prepare($query_order);
            
            if (!$stmt->execute([':uid' => $user_id, ':total' => $total, ':addr' => $address])) {
                throw new Exception("Gagal Insert Order: " . implode(" ", $stmt->errorInfo()));
            }
            
            $order_id = $this->conn->lastInsertId();

            // INSERT DETAILS & UPDATE STOK
            $query_detail = "INSERT INTO order_details (order_id, book_id, quantity, price_at_purchase) VALUES (:oid, :bid, :qty, :price)";
            $query_stock = "UPDATE books SET stock = stock - :qty WHERE id = :bid";
            
            $stmt_detail = $this->conn->prepare($query_detail);
            $stmt_stock = $this->conn->prepare($query_stock);

            foreach ($cart_items as $item) {
                // Cek stok dulu sebelum kurangi
                if ($item['quantity'] > 0) {
                    if (!$stmt_detail->execute([
                        ':oid' => $order_id, 
                        ':bid' => $item['book_id'], 
                        ':qty' => $item['quantity'], 
                        ':price' => $item['price']
                    ])) {
                        throw new Exception("Gagal Insert Detail: " . implode(" ", $stmt_detail->errorInfo()));
                    }

                    if (!$stmt_stock->execute([':qty' => $item['quantity'], ':bid' => $item['book_id']])) {
                        throw new Exception("Gagal Update Stok: " . implode(" ", $stmt_stock->errorInfo()));
                    }
                }
            }

            // KOSONGKAN KERANJANG
            $this->conn->prepare("DELETE FROM cart WHERE user_id = :uid")->execute([':uid' => $user_id]);

            $this->conn->commit();
            return true; // Sukses!

        } catch (Exception $e) {
            $this->conn->rollBack();
            return "ERROR SQL: " . $e->getMessage(); // Kembalikan pesan error asli!
        }
    }
}
?>