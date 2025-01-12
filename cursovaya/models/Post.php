<?php
class Post {
    private $conn;
    private $table_name = "portfolio_posts";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Получение последних постов
    public function getRecentPosts($page = 1, $per_page = 10, $category = null) {
        $offset = ($page - 1) * $per_page;
        
        $query = "SELECT p.*, u.username, f.file_path, f.file_type 
                 FROM " . $this->table_name . " p
                 LEFT JOIN users u ON p.user_id = u.user_id
                 LEFT JOIN post_files f ON p.post_id = f.post_id";
        
        if ($category) {
            $query .= " WHERE p.category = :category";
        }
        
        $query .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        if ($category) {
            $stmt->bindParam(':category', $category);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Создание нового поста
    public function create($user_id, $title, $content, $category, $files = []) {
        $query = "INSERT INTO " . $this->table_name . "
                 SET user_id = :user_id,
                     title = :title,
                     content = :content,
                     category = :category";
                     
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':category', $category);
        
        if ($stmt->execute()) {
            $post_id = $this->conn->lastInsertId();
            
            // Сохраняем файлы
            if (!empty($files)) {
                $this->saveFiles($post_id, $files);
            }
            
            return $post_id;
        }
        
        return false;
    }

    // Сохранение файлов
    private function saveFiles($post_id, $files) {
        foreach ($files as $file) {
            if (!isset($file['content'])) {
                $file['content'] = base64_encode(file_get_contents($file['tmp_name']));
            }
            
            $query = "INSERT INTO post_files
                     SET post_id = :post_id,
                         file_name = :file_name,
                         file_type = :file_type,
                         file_content = :file_content";
                         
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':post_id' => $post_id,
                ':file_name' => $file['name'],
                ':file_type' => $file['type'],
                ':file_content' => $file['content']
            ]);
        }
    }

    // Получение всех категорий
    public function getAllCategories() {
        $query = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPostById($id) {
        $query = "SELECT p.*, u.username, GROUP_CONCAT(f.file_path) as files,
                  GROUP_CONCAT(f.file_type) as file_types,
                  GROUP_CONCAT(f.file_name) as file_names
                  FROM " . $this->table_name . " p
                  LEFT JOIN users u ON p.user_id = u.user_id
                  LEFT JOIN post_files f ON p.post_id = f.post_id
                  WHERE p.post_id = :id
                  GROUP BY p.post_id";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($post) {
            // Преобразуем строки с файлами в массивы
            if ($post['files']) {
                $files = explode(',', $post['files']);
                $types = explode(',', $post['file_types']);
                $names = explode(',', $post['file_names']);
                
                $post['files'] = [];
                for ($i = 0; $i < count($files); $i++) {
                    $post['files'][] = [
                        'file_path' => $files[$i],
                        'file_type' => $types[$i],
                        'file_name' => $names[$i]
                    ];
                }
            } else {
                $post['files'] = [];
            }
        }
        
        return $post;
    }

    public function getUserPosts($user_id) {
        $query = "SELECT p.*, p.user_id, c.name as category, 
                  f.file_type, f.file_name, f.file_content
                  FROM " . $this->table_name . " p
                  LEFT JOIN categories c ON p.category = c.category_id
                  LEFT JOIN post_files f ON p.post_id = f.post_id
                  WHERE p.user_id = :user_id
                  ORDER BY p.created_at DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $posts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!isset($posts[$row['post_id']])) {
                $posts[$row['post_id']] = [
                    'post_id' => $row['post_id'],
                    'user_id' => $row['user_id'],
                    'title' => $row['title'],
                    'content' => $row['content'],
                    'category' => $row['category'],
                    'created_at' => $row['created_at'],
                    'files' => []
                ];
            }
            
            if ($row['file_content']) {
                $posts[$row['post_id']]['files'][] = [
                    'file_type' => $row['file_type'],
                    'file_name' => $row['file_name'],
                    'file_content' => $row['file_content']
                ];
            }
        }
        
        return array_values($posts);
    }

    // Добавим методы для управления постами
    public function deletePost($post_id, $user_id) {
        // Проверяем, принадлежит ли пост пользователю
        $query = "SELECT user_id FROM " . $this->table_name . " WHERE post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post || ($post['user_id'] != $user_id && !isAdmin())) {
            return false;
        }

        // Сначала удаляем файлы
        $query = "SELECT file_path FROM post_files WHERE post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        
        while ($file = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (file_exists($file['file_path'])) {
                unlink($file['file_path']);
            }
        }

        // Удаляем записи о файлах
        $query = "DELETE FROM post_files WHERE post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        // Удаляем сам пост
        $query = "DELETE FROM " . $this->table_name . " WHERE post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        return $stmt->execute();
    }

    public function updatePost($post_id, $user_id, $title, $content, $category, $files = []) {
        // Проверяем права
        if (!$this->checkPostOwnership($post_id, $user_id)) {
            return false;
        }

        $query = "UPDATE " . $this->table_name . "
                  SET title = :title,
                      content = :content,
                      category = :category
                  WHERE post_id = :post_id";
                  
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':category' => $category,
            ':post_id' => $post_id
        ]);

        // Если есть новые файлы, добавляем их
        if ($result && !empty($files)) {
            $this->saveFiles($post_id, $files);
        }

        return $result;
    }

    private function checkPostOwnership($post_id, $user_id) {
        $query = "SELECT user_id FROM " . $this->table_name . " WHERE post_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$post_id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $post && ($post['user_id'] == $user_id || isAdmin());
    }

    public function deleteFile($file_id) {
        $query = "DELETE FROM post_files WHERE file_id = :file_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':file_id' => $file_id]);
    }
}
?> 