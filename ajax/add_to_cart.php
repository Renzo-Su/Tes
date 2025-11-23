<?php
// ajax/add_to_cart.php
session_start();
require_once '../classes/Database.php'; // Kita butuh akses DB langsung untuk cek cart existing

$response = ['success' => false, 'message' => 'Error'];

if (isset($_SESSION['user_id']) && isset($_POST['book_id'])) {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    $db = new Database();
    $conn = $db->getConnection();

    // 1. Ambil Data Stok Buku Real-time
    $stmtBook = $conn->prepare("SELECT stock, title FROM books WHERE id = :id");
    $stmtBook->execute([':id' => $book_id]);
    $bookData = $stmtBook->fetch(PDO::FETCH_ASSOC);

    if (!$bookData) {
        echo json_encode(['success' => false, 'message' => 'Buku tidak ditemukan.']);
        exit;
    }

    // 2. Cek apakah buku ini SUDAH ada di keranjang user?
    $stmtCart = $conn->prepare("SELECT quantity FROM cart WHERE user_id = :uid AND book_id = :bid");
    $stmtCart->execute([':uid' => $user_id, ':bid' => $book_id]);
    $existingCart = $stmtCart->fetch(PDO::FETCH_ASSOC);
    
    $qty_in_cart = $existingCart ? (int)$existingCart['quantity'] : 0;
    
    // 3. Hitung Total yang diinginkan (Yang sudah ada + Yang mau ditambah)
    $total_wanted = $qty_in_cart + $quantity;
    $current_stock = (int)$bookData['stock'];

    // 4. Validasi Ketat
    if ($current_stock <= 0) {
        $response['message'] = 'Stok habis!';
    } 
    elseif ($total_wanted > $current_stock) {
        // Pesan detail: Memberitahu user sisa jatah mereka
        $sisa_jatah = $current_stock - $qty_in_cart;
        if ($sisa_jatah <= 0) {
            $response['message'] = "Anda sudah memborong semua stok buku ini di keranjang Anda!";
        } else {
            $response['message'] = "Stok tidak cukup! Anda sudah punya $qty_in_cart di keranjang. Sisa stok hanya $current_stock. Anda hanya bisa menambah $sisa_jatah lagi.";
        }
    } 
    else {
        // 5. Jika Lolos, Masukkan ke Keranjang (Logic Insert/Update)
        if ($existingCart) {
            $query = "UPDATE cart SET quantity = :qty WHERE user_id = :uid AND book_id = :bid";
            $stmt = $conn->prepare($query);
            $res = $stmt->execute([':qty' => $total_wanted, ':uid' => $user_id, ':bid' => $book_id]);
        } else {
            $query = "INSERT INTO cart (user_id, book_id, quantity) VALUES (:uid, :bid, :qty)";
            $stmt = $conn->prepare($query);
            $res = $stmt->execute([':uid' => $user_id, ':bid' => $book_id, ':qty' => $quantity]);
        }

        if ($res) {
            $response['success'] = true;
            $response['message'] = "Berhasil masuk keranjang!";
            
            // Hitung total item untuk badge navbar
            $stmtCount = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = :uid");
            $stmtCount->execute([':uid' => $user_id]);
            $dataCount = $stmtCount->fetch(PDO::FETCH_ASSOC);
            $response['total_items'] = $dataCount['total'] ? $dataCount['total'] : 0;
        } else {
            $response['message'] = 'Gagal menyimpan ke database.';
        }
    }

} else {
    $response['message'] = 'Silakan login terlebih dahulu.';
}

echo json_encode($response);
?>