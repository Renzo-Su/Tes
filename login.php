<?php
// login.php
require_once 'classes/Auth.php';

// Panggil header
include_once 'template/header.php';

// Jika sudah login, lempar ke halaman utama (atau admin jika role admin)
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        echo "<script>window.location.href='admin/index.php';</script>";
    } else {
        echo "<script>window.location.href='index.php';</script>";
    }
    exit;
}

$message = "";

// Proses Login saat tombol ditekan
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $auth = new Auth();

    if ($auth->login($username, $password)) {
        // Login berhasil
        // Redirect sesuai role
        if ($_SESSION['role'] == 'admin') {
            echo "<script>window.location.href='admin/index.php';</script>";
        } else {
            echo "<script>window.location.href='index.php';</script>";
        }
        exit;
    } else {
        $message = '<div class="alert alert-danger">Login gagal! Username atau password salah.</div>';
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow mt-5">
            <div class="card-header bg-success text-white text-center">
                <h4>Login Pengguna</h4>
            </div>
            <div class="card-body">
                <?php echo $message; ?>
                
                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-success w-100">Masuk</button>
                </form>
            </div>
            <div class="card-footer text-center">
                Belum punya akun? <a href="register.php">Daftar disini</a>
            </div>
        </div>
    </div>
</div>

<?php include_once 'template/footer.php'; ?>