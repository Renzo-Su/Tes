<?php
// Kita panggil file Database.php
require_once 'Database.php';

class Auth {
    private $db;
    private $conn;

    public function __construct() {
        // Buat objek database baru
        $this->db = new Database();
        // Dapatkan koneksinya
        $this->conn = $this->db->getConnection();
    }

    // Method untuk registrasi user
    public function register($username, $password) {
        try {
            // 1. HASH PASSWORD! Ini adalah langkah keamanan KRUSIAL.
            // Jangan pernah simpan password sebagai teks biasa.
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // 2. Buat query INSERT (Gunakan prepared statement untuk keamanan)
            $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, 'user')";
            
            // 3. Persiapkan statement
            $stmt = $this->conn->prepare($query);

            // 4. Bind (ikat) parameter
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);

            // 5. Eksekusi statement
            if ($stmt->execute()) {
                return true; // Registrasi berhasil
            } else {
                return false; // Gagal eksekusi
            }

        } catch (PDOException $e) {
            // Tangani error, misal jika username sudah ada (UNIQUE constraint)
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Method untuk login user
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function login($username, $password) {
        try {
            // 1. Cari user berdasarkan username
            $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            // 2. Cek apakah user ditemukan
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // 3. Verifikasi password (hash vs input)
                if (password_verify($password, $user['password'])) {
                    // Password benar! Simpan data ke SESSION
                    
                    // Pastikan session sudah start (biasanya di header, tapi untuk aman kita set di sini jika belum)
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role']; // Penting untuk Admin
                    
                    return true;
                }
            }
            
            return false; // Username tidak ada ATAU password salah
            
        } catch (PDOException $e) {
            return false;
        }
    }   
}
?>