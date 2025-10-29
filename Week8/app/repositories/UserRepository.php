<?php

/**
 * Week 8: User Repository
 * Data access layer for User operations
 */

class UserRepository
{
    private $db;
    private $config;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->config = require __DIR__ . '/../config.php';
    }

    /**
     * Find user by ID
     * 
     * @param int $id
     * @return User|null
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        if ($row = $stmt->fetch()) {
            return User::fromArray($row);
        }
        
        return null;
    }

    /**
     * Find user by username
     * 
     * @param string $username
     * @return User|null
     */
    public function findByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        
        if ($row = $stmt->fetch()) {
            return User::fromArray($row);
        }
        
        return null;
    }

    /**
     * Find user by email
     * 
     * @param string $email
     * @return User|null
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        
        if ($row = $stmt->fetch()) {
            return User::fromArray($row);
        }
        
        return null;
    }

    /**
     * Find user by username or email
     * 
     * @param string $identifier
     * @return User|null
     */
    public function findByUsernameOrEmail($identifier)
    {
        $sql = "SELECT * FROM users 
                WHERE (username = ? OR email = ?) 
                AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$identifier, $identifier]);
        
        if ($row = $stmt->fetch()) {
            return User::fromArray($row);
        }
        
        return null;
    }

    /**
     * Get all users
     * 
     * @param string|null $role Filter by role
     * @return array
     */
    public function getAll($role = null)
    {
        $sql = "SELECT * FROM users WHERE deleted_at IS NULL";
        $params = [];
        
        if ($role) {
            $sql .= " AND role = ?";
            $params[] = $role;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $users = [];
        while ($row = $stmt->fetch()) {
            $users[] = User::fromArray($row);
        }
        
        return $users;
    }

    /**
     * Alias for getAll() - for consistency
     * 
     * @param string|null $role Filter by role
     * @return array
     */
    public function findAll($role = null)
    {
        return $this->getAll($role);
    }

    /**
     * Create new user
     * 
     * @param array $data
     * @return User
     */
    public function create($data)
    {
        $sql = "INSERT INTO users (username, email, password, full_name, role, is_active) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['username'],
            $data['email'],
            User::hashPassword($data['password']),
            $data['full_name'],
            $data['role'] ?? 'receptionist',
            $data['is_active'] ?? true
        ]);
        
        $userId = $this->db->lastInsertId();
        return $this->findById($userId);
    }

    /**
     * Update user
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = [];
        
        if (isset($data['username'])) {
            $fields[] = "username = ?";
            $params[] = $data['username'];
        }
        
        if (isset($data['email'])) {
            $fields[] = "email = ?";
            $params[] = $data['email'];
        }
        
        if (isset($data['password'])) {
            $fields[] = "password = ?";
            $params[] = User::hashPassword($data['password']);
        }
        
        if (isset($data['full_name'])) {
            $fields[] = "full_name = ?";
            $params[] = $data['full_name'];
        }
        
        if (isset($data['role'])) {
            $fields[] = "role = ?";
            $params[] = $data['role'];
        }
        
        if (isset($data['is_active'])) {
            $fields[] = "is_active = ?";
            $params[] = $data['is_active'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Update last login timestamp
     * 
     * @param int $id
     * @return bool
     */
    public function updateLastLogin($id)
    {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Soft delete user
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $sql = "UPDATE users SET deleted_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Check if username exists
     * 
     * @param string $username
     * @param int|null $excludeId
     * @return bool
     */
    public function usernameExists($username, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE username = ? AND deleted_at IS NULL";
        $params = [$username];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Check if email exists
     * 
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = ? AND deleted_at IS NULL";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Get user count by role
     * 
     * @return array
     */
    public function countByRole()
    {
        $sql = "SELECT role, COUNT(*) as count 
                FROM users 
                WHERE deleted_at IS NULL 
                GROUP BY role";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
