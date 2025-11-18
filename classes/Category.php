<?php
// classes/Category.php
require_once 'Database.php';

class Category {
    private $conn;
    private $table_name = "categories";

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY category_name ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // --- TAMBAHKAN FUNGSI INI ---
    /**
     * Method untuk menambah kategori baru
     */
    public function create($category_name) {
        try {
            $query = "INSERT INTO " . $this->table_name . " (category_name) VALUES (:name)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $category_name);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Kemungkinan error karena nama kembar (UNIQUE constraint)
            return false;
        }
    }
}
?>