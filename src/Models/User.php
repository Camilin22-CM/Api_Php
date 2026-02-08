<?php
/**
 * Modelo User
 */

namespace Src\Models;

use Src\Utils\Database;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Crear tabla de usuarios si no existe
     */
    public function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(100) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    phone VARCHAR(20),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
        
        $this->db->query($sql);
    }

    /**
     * Crear nuevo usuario
     */
    public function create($name, $email, $password, $phone = null)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO users (name, email, password, phone) 
                VALUES (?, ?, ?, ?)";
        
        $userId = $this->db->insert($sql, [$name, $email, $hashedPassword, $phone]);
        
        return [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ];
    }

    /**
     * Buscar usuario por email
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        return $this->db->getOne($sql, [$email]);
    }

    /**
     * Buscar usuario por ID
     */
    public function findById($id)
    {
        $sql = "SELECT id, name, email, phone, created_at FROM users WHERE id = ?";
        return $this->db->getOne($sql, [$id]);
    }

    /**
     * Verificar contraseña
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Obtener todos los usuarios
     */
    public function getAll()
    {
        $sql = "SELECT id, name, email, phone, created_at FROM users";
        return $this->db->getAll($sql);
    }

    /**
     * Actualizar usuario
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = [];

        foreach ($data as $key => $value) {
            if (in_array($key, ['name', 'phone'])) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $params[] = $id;
        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
        
        return $this->db->update($sql, $params) > 0;
    }

    /**
     * Eliminar usuario
     */
    public function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->db->delete($sql, [$id]) > 0;
    }

    /**
     * Verificar si email existe
     */
    public function emailExists($email)
    {
        return $this->findByEmail($email) !== false;
    }
}
