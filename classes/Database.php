<?php

class Database {
    //Koneksi Database
    private $host = "localhost";        
    private $db_name = "db_tokobuku";  
    private $username = "root";         
    private $password = "";             
    public $conn;                       

    // Method untuk mendapatkan koneksi
    public function getConnection() {
        $this->conn = null;

        try {
            // Membuat koneksi PDO baru
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            
            // Set error mode PDO ke exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $exception) {
            // Jika koneksi gagal, tampilkan pesan error
            echo "Koneksi database gagal: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>