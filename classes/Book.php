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

    /**
     * 
     * @return array
     */
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
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    /**
     * 
     * @param int $id
     * @return mixed
     */
    public function getById($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * 
     * @param array $data (dari $_POST)
     * @param array $file (dari $_FILES)
     * @return bool
     */
    public function create($data, $file) {
        try {
            // 1. Handle File Upload
            $image_filename = $this->uploadCover($file);
            if ($image_filename === false) {
                return false;
            }

            // 2. Query INSERT
            $query = "INSERT INTO " . $this->table_name . "
                      (title, author, description, price, stock, category_id, cover_image)
                      VALUES
                      (:title, :author, :description, :price, :stock, :category_id, :cover_image)";
            
            $stmt = $this->conn->prepare($query);

            // Bind data
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':author', $data['author']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':price', $data['price']);
            $stmt->bindParam(':stock', $data['stock']);
            $stmt->bindParam(':category_id', $data['category_id']);
            $stmt->bindParam(':cover_image', $image_filename);

            return $stmt->execute();

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Method untuk mengupdate data buku
     * @param array $data (dari $_POST)
     * @param array $file (dari $_FILES, opsional)
     * @return bool
     */
    public function update($data, $file) {
        try {
            $image_filename = $data['old_cover_image']; // Nama gambar lama
            $new_image_uploaded = false;

            // Cek apakah ada file baru yang di-upload
            if (isset($file['name']) && $file['error'] == UPLOAD_ERR_OK) {
                $new_filename = $this->uploadCover($file);
                if ($new_filename) {
                    $image_filename = $new_filename; // Menggunakan nama file baru
                    $new_image_uploaded = true;
                }
            }

            $query = "UPDATE " . $this->table_name . "
                      SET title = :title, author = :author, description = :description,
                          price = :price, stock = :stock, category_id = :category_id,
                          cover_image = :cover_image
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);

            // Bind data
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':author', $data['author']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':price', $data['price']);
            $stmt->bindParam(':stock', $data['stock']);
            $stmt->bindParam(':category_id', $data['category_id']);
            $stmt->bindParam(':cover_image', $image_filename);
            $stmt->bindParam(':id', $data['id']);

            if ($stmt->execute()) {
                // Jika update berhasil DAN ada gambar baru yg diupload
                // Hapus gambar lama (kecuali gambar lama adalah 'default.jpg')
                if ($new_image_uploaded && $data['old_cover_image'] != 'default.jpg' && !empty($data['old_cover_image'])) {
                    $this->deleteCoverFile($data['old_cover_image']);
                }
                return true;
            }
            return false;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Method untuk menghapus buku
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        try {
            // 1. Ambil data buku dulu untuk dapat nama filenya
            $book = $this->getById($id);
            if (!$book) return false; // Buku tidak ditemukan

            // 2. Query DELETE
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                // 3. Jika berhasil, hapus file gambar
                $this->deleteCoverFile($book['cover_image']);
                return true;
            }
            return false;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }


    // --- Helper Methods (Private) ---

    /**
     * Helper untuk upload file gambar
     * @param array $file (dari $_FILES['cover_image'])
     * @return string|bool (Nama file jika berhasil, false jika gagal)
     */
    private function uploadCover($file) {
        // Cek jika tidak ada file di-upload atau ada error
        if (!isset($file['name']) || $file['error'] != UPLOAD_ERR_OK) {
            return 'default.jpg'; // Kembalikan nama default jika tidak ada yg diupload
        }

        $target_dir = "../assets/images/"; // Target direktori (INGAT: ../)
        $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        // Buat nama file unik: "cover_TIMESTAMP.EXT"
        $new_filename = "cover_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;

        // Validasi (Cek jika ini file gambar asli)
        $check = getimagesize($file["tmp_name"]);
        if($check === false) {
            echo "Error: File bukan gambar.";
            return false;
        }

        // Validasi (Batasi tipe file)
        if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg") {
            echo "Error: Hanya format JPG, JPEG, & PNG yang diizinkan.";
            return false;
        }

        // Coba pindahkan file
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $new_filename; // Berhasil, kembalikan nama file baru
        } else {
            echo "Error: Gagal memindahkan file.";
            return false;
        }
    }

    /**
     * Helper untuk menghapus file gambar dari server
     * @param string $filename
     */
    private function deleteCoverFile($filename) {
        if (empty($filename) || $filename == 'default.jpg') {
            return; // Jangan hapus file default
        }

        $filepath = "../assets/images/" . $filename;
        if (file_exists($filepath)) {
            unlink($filepath); // Hapus file
        }
    }
}
?>