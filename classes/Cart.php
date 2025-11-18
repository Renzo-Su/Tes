<?php
// classes/Cart.php
require_once 'Database.php';

class Cart {
    private $conn;
    private $table_name = "cart";

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Method utama: Menambah/update item di keranjang
     * @param int $user_id
     * @param int $book_id
     * @param int $quantity
     * @return bool
     */
    public function addItem($user_id, $book_id, $quantity = 1) {
        try {
            // 1. Cek dulu apakah buku ini sudah ada di keranjang user
            $query_check = "SELECT * FROM " . $this->table_name . " 
                            WHERE user_id = :user_id AND book_id = :book_id";
            $stmt_check = $this->conn->prepare($query_check);
            $stmt_check->bindParam(':user_id', $user_id);
            $stmt_check->bindParam(':book_id', $book_id);
            $stmt_check->execute();

            if ($stmt_check->rowCount() > 0) {
                // --- JIKA SUDAH ADA: UPDATE quantity-nya ---
                $cart_item = $stmt_check->fetch(PDO::FETCH_ASSOC);
                $new_quantity = $cart_item['quantity'] + $quantity;
                
                $query_update = "UPDATE " . $this->table_name . " 
                                 SET quantity = :quantity 
                                 WHERE id = :cart_id";
                $stmt_update = $this->conn->prepare($query_update);
                $stmt_update->bindParam(':quantity', $new_quantity);
                $stmt_update->bindParam(':cart_id', $cart_item['id']);
                return $stmt_update->execute();

            } else {
                // --- JIKA BELUM ADA: INSERT baru ---
                $query_insert = "INSERT INTO " . $this->table_name . " 
                                 (user_id, book_id, quantity) 
                                 VALUES (:user_id, :book_id, :quantity)";
                $stmt_insert = $this->conn->prepare($query_insert);
                $stmt_insert->bindParam(':user_id', $user_id);
                $stmt_insert->bindParam(':book_id', $book_id);
                $stmt_insert->bindParam(':quantity', $quantity);
                return $stmt_insert->execute();
            }
            

        } catch (PDOException $e) {
            // error_log("Cart Error: " . $e->getMessage());
            return false;
        }
    }
    /**
     * Method untuk mengambil semua item di keranjang user tertentu
     * (Menggunakan JOIN agar kita juga dapat Judul, Harga, dan Gambar buku)
     */
    public function getCartItems($user_id) {
        try {
            $query = "SELECT c.id as cart_id, c.quantity, 
                             b.id as book_id, b.title, b.price, b.cover_image 
                      FROM " . $this->table_name . " c
                      JOIN books b ON c.book_id = b.id
                      WHERE c.user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Method untuk menghapus item dari keranjang
     */
    public function removeItem($cart_id, $user_id) {
        try {
            // Pastikan item itu milik user yang sedang login (keamanan)
            $query = "DELETE FROM " . $this->table_name . " 
                      WHERE id = :cart_id AND user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':cart_id', $cart_id);
            $stmt->bindParam(':user_id', $user_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    /**
     * Method untuk update quantity secara spesifik
     */
    public function updateQuantity($cart_id, $quantity, $user_id) {
        if ($quantity < 1) return false; // Minimal 1
        
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET quantity = :qty 
                      WHERE id = :cid AND user_id = :uid";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':qty', $quantity);
            $stmt->bindParam(':cid', $cart_id);
            $stmt->bindParam(':uid', $user_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

}
?>