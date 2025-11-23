<?php
// classes/Book.php
require_once 'Database.php';

class Book {
    private $conn;
    private $table_name = "books";

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Ambil semua buku
    public function getAll() {
        try {
            $query = "SELECT b.*, c.category_name 
                      FROM " . $this->table_name . " b
                      LEFT JOIN categories c ON b.category_id = c.id
                      ORDER BY b.id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // --- [BARU] FITUR PENCARIAN ---
    public function search($keyword) {
        try {
            $query = "SELECT b.*, c.category_name 
                      FROM " . $this->table_name . " b
                      LEFT JOIN categories c ON b.category_id = c.id
                      WHERE b.title LIKE :keyword OR b.author LIKE :keyword
                      ORDER BY b.id DESC";
            
            $stmt = $this->conn->prepare($query);
            $keyword = "%{$keyword}%"; // Tambahkan wildcard % untuk SQL LIKE
            $stmt->bindParam(':keyword', $keyword);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Ambil 1 buku by ID
    public function getById($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return false; }
    }

    // Create (Tambah Buku)
    public function create($data, $file) {
        try {
            $image_filename = $this->uploadCover($file);
            if ($image_filename === false) return false;

            $query = "INSERT INTO " . $this->table_name . "
                      (title, author, description, price, stock, category_id, cover_image)
                      VALUES (:title, :author, :description, :price, :stock, :cat_id, :img)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':title' => $data['title'],
                ':author' => $data['author'],
                ':description' => $data['description'],
                ':price' => $data['price'],
                ':stock' => $data['stock'],
                ':cat_id' => $data['category_id'],
                ':img' => $image_filename
            ]);
            return true;
        } catch (PDOException $e) { return false; }
    }

    // Update Buku
    public function update($data, $file) {
        try {
            $image_filename = $data['old_cover_image'];
            $new_upload = false;

            if (isset($file['name']) && $file['error'] == UPLOAD_ERR_OK) {
                $new_filename = $this->uploadCover($file);
                if ($new_filename) {
                    $image_filename = $new_filename;
                    $new_upload = true;
                }
            }

            $query = "UPDATE " . $this->table_name . "
                      SET title=:title, author=:author, description=:desc,
                          price=:price, stock=:stock, category_id=:cat_id, cover_image=:img
                      WHERE id=:id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':title' => $data['title'],
                ':author' => $data['author'],
                ':desc' => $data['description'],
                ':price' => $data['price'],
                ':stock' => $data['stock'],
                ':cat_id' => $data['category_id'],
                ':img' => $image_filename,
                ':id' => $data['id']
            ]);

            if ($new_upload && $data['old_cover_image'] != 'default.jpg') {
                $this->deleteCoverFile($data['old_cover_image']);
            }
            return true;
        } catch (PDOException $e) { return false; }
    }

    // Delete Buku
    public function delete($id) {
        try {
            $book = $this->getById($id);
            if (!$book) return false;

            $stmt = $this->conn->prepare("DELETE FROM " . $this->table_name . " WHERE id = :id");
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                $this->deleteCoverFile($book['cover_image']);
                return true;
            }
            return false;
        } catch (PDOException $e) { return false; }
    }

    // --- Private Helpers ---
    private function uploadCover($file) {
        if (!isset($file['name']) || $file['error'] != UPLOAD_ERR_OK) return 'default.jpg';
        
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $new_filename = "cover_" . time() . "." . $ext;
        $target = "../assets/images/" . $new_filename;

        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) return false;
        if (move_uploaded_file($file["tmp_name"], $target)) return $new_filename;
        return false;
    }

    private function deleteCoverFile($filename) {
        if (!empty($filename) && $filename != 'default.jpg' && file_exists("../assets/images/$filename")) {
            unlink("../assets/images/$filename");
        }
    }
}
?>