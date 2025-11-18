<?php
// ajax/add_to_cart.php
session_start();
require_once '../classes/Cart.php';

$response = ['success' => false, 'message' => 'Error', 'total_items' => 0];

if (isset($_SESSION['user_id']) && isset($_POST['book_id'])) {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    
    $cart = new Cart();
    if ($cart->addItem($user_id, $book_id, 1)) {
        $response['success'] = true;
        $response['message'] = 'Berhasil masuk keranjang!';
        
        // HITUNG TOTAL ITEM TERBARU
        // Kita buat query manual cepat di sini atau panggil method di Cart
        // Agar simpel, kita hitung manual:
        $items = $cart->getCartItems($user_id);
        $count = 0;
        foreach($items as $item) {
            $count += $item['quantity'];
        }
        $response['total_items'] = $count;
    }
} else {
    $response['message'] = 'Silakan login terlebih dahulu.';
}

echo json_encode($response);
?>