<?php
class User {
    private $conn;
    private $table_name = "users";

    public $user_id;
    public $username;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $role_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Регистрация нового пользователя
    public function register() {
        // Проверяем существование email
        if($this->emailExists($this->email)) {
            return array(
                "success" => false,
                "message" => "Этот email уже зарегистрирован"
            );
        }

        $query = "INSERT INTO " . $this->table_name . "
                SET
                    username = :username,
                    email = :email,
                    password_hash = :password,
                    first_name = :first_name,
                    last_name = :last_name,
                    role_id = 2"; // 2 - роль обычного пользователя

        $stmt = $this->conn->prepare($query);

        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);

        try {
            $stmt->execute();
            return array(
                "success" => true,
                "message" => "Регистрация успешна"
            );
        } catch(PDOException $e) {
            return array(
                "success" => false,
                "message" => "Ошибка регистрации: " . $e->getMessage()
            );
        }
    }

    // Авторизация пользователя
    public function login($username, $password) {
        $query = "SELECT user_id, username, password_hash, role_id
                FROM " . $this->table_name . "
                WHERE username = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row && password_verify($password, $row['password_hash'])) {
            return $row;
        }
        return false;
    }

    // Получение данных пользователя по ID
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Обновление данных пользователя
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET
                    username = :username,
                    email = :email,
                    first_name = :first_name,
                    last_name = :last_name";
        
        // Добавляем обновление пароля, если он был предоставлен
        if (isset($this->password)) {
            $query .= ", password_hash = :password";
        }
        
        $query .= " WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":user_id", $this->user_id);
        
        if (isset($this->password)) {
            $stmt->bindParam(":password", $this->password);
        }

        return $stmt->execute();
    }

    // Удаление пользователя (только для администраторов)
    public function delete($user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        return $stmt->execute();
    }

    // Получение списка всех пользователей
    public function getAllUsers($includeHidden = false) {
        $query = "SELECT user_id, username, first_name, last_name, is_hidden, avatar_image 
                  FROM " . $this->table_name;
        
        if (!$includeHidden) {
            $query .= " WHERE is_hidden = FALSE";
        }
        
        $query .= " ORDER BY username";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Проверка существования email
    public function emailExists($email) {
        $query = "SELECT user_id FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Добавим метод для изменения видимости профиля
    public function toggleVisibility($user_id) {
        $query = "UPDATE " . $this->table_name . "
                  SET is_hidden = NOT is_hidden
                  WHERE user_id = :user_id";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }

    public function updateProfile($data) {
        $query = "UPDATE " . $this->table_name . " 
                SET username = :username,
                    email = :email,
                    first_name = :first_name,
                    last_name = :last_name,
                    bio = :bio";
        
        // Если загружен новый аватар
        if (isset($data['avatar_image'])) {
            $query .= ", avatar_image = :avatar_image";
        }
        
        // Если указан новый пароль
        if (!empty($data['new_password'])) {
            $query .= ", password_hash = :password_hash";
        }
        
        $query .= " WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        
        $params = [
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':bio' => $data['bio'],
            ':user_id' => $data['user_id']
        ];
        
        if (isset($data['avatar_image'])) {
            $params[':avatar_image'] = $data['avatar_image'];
        }
        
        if (!empty($data['new_password'])) {
            $params[':password_hash'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
        }
        
        return $stmt->execute($params);
    }
}
?> 