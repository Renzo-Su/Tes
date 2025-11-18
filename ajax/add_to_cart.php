<?php
// ajax/add_to_cart.php
session_start();
require_once '../classes/Cart.php';
require_once '../classes/Book.php'; // Tambahkan ini untuk cek stok real-time

$response = ['success' => false, 'message' => 'Error'];

if (isset($_SESSION['user_id']) && isset($_POST['book_id'])) {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // 1. Ambil Data Stok Terbaru dari Database
    $bookObj = new Book();
    $bookData = $bookObj->getById($book_id);

    if (!$bookData) {
        $response['message'] = 'Buku tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    $current_stock = (int)$bookData['stock'];

    // 2. Validasi Logic: Apakah stok cukup?
    if ($current_stock <= 0) {
        $response['message'] = 'Maaf, Stok buku ini sudah habis!';
    } 
    elseif ($quantity > $current_stock) {
        // Jika user maksa input 4 padahal stok 1
        $response['message'] = "Stok tidak cukup! Tersisa hanya $current_stock buku.";
    } 
    else {
        // 3. Jika lolos validasi, baru masukkan ke keranjang
        $cart = new Cart();
        if ($cart->addItem($user_id, $book_id, $quantity)) {
            $response['success'] = true;
            $response['message'] = "Berhasil masuk keranjang!";
            
            // Hitung badge
            $items = $cart->getCartItems($user_id);
            $count = 0;
            foreach($items as $item) $count += $item['quantity'];
            $response['total_items'] = $count;
        } else {
            $response['message'] = 'Gagal menyimpan ke database.';
        }
    }

} else {
    $response['message'] = 'Silakan login terlebih dahulu.';
}

echo json_encode($response);
?>