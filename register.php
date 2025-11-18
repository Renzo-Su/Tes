<?php
require_once 'classes/Auth.php';

$message = "";

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Cek apakah field ada yang kosong
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $message = '<div class="alert alert-danger">Error: Semua field wajib diisi!</div>';
    }
    // Cek apakah password dan konfirmasi password cocok
    elseif ($password !== $confirm_password) {
        $message = '<div class="alert alert-danger">Error: Konfirmasi password tidak cocok!</div>';
    } 
    // Cek panjang minimum password
    elseif (strlen($password) < 6) {
        $message = '<div class="alert alert-danger">Error: Password minimal harus 6 karakter!</div>';
    }

    else {
        $auth = new Auth();

        // Panggil method 'register' dari class Auth
        if ($auth->register($username, $password)) {
            // Jika registrasi berhasil
            $message = '<div class="alert alert-success">Registrasi berhasil! Silakan <a href="login.php">login</a>.</div>';
        } else {
            // Jika gagal (kemungkinan besar username sudah digunakan)
            $message = '<div class="alert alert-danger">Registrasi gagal. Username mungkin sudah terdaftar.</div>';
        }
    }
}

include_once 'template/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Registrasi Pengguna Baru</h4>
            </div>
            <div class="card-body">
                
                <?php 
                // Tampilkan pesan sukses atau error (jika $message tidak kosong)
                echo $message;
                ?>

                <form action="register.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" name="register" class="btn btn-primary w-100">Daftar</button>
                </form>

            </div>
            <div class="card-footer text-center">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </div>
        </div>
    </div>
</div>

<?php 
include_once 'template/footer.php'; 
?>