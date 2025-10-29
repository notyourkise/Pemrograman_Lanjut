<?php

/**
 * Week 8: Auth Helper
 * Singleton pattern for authentication management
 * 
 * This class handles:
 * - User login/logout
 * - Session management
 * - Role-based access control
 * - Permission checking
 */

class Auth
{
    private static $instance = null;
    private $userRepository;
    private $config;
    private $currentUser = null;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->config = require __DIR__ . '/../config.php';
        $this->initSession();
        $this->loadCurrentUser();
    }

    /**
     * Get singleton instance
     * 
     * @return Auth
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize session with security settings
     */
    private function initSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Set session name from config
            session_name($this->config['security']['session_name']);
            
            // Security settings
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
            
            session_start();
            
            // Regenerate session ID periodically
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
            } else if (time() - $_SESSION['created'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }

    /**
     * Load current user from session
     */
    private function loadCurrentUser()
    {
        if (isset($_SESSION['user_id'])) {
            $this->currentUser = $this->userRepository->findById($_SESSION['user_id']);
            
            // If user not found or inactive, logout
            if (!$this->currentUser || !$this->currentUser->isActive()) {
                $this->logout();
            }
        }
    }

    /**
     * Attempt to login user
     * 
     * @param string $identifier Username or email
     * @param string $password
     * @param bool $remember Remember me option
     * @return bool
     */
    public function attempt($identifier, $password, $remember = false)
    {
        // Check for too many failed attempts
        if ($this->isLockedOut()) {
            return false;
        }

        // Find user by username or email
        $user = $this->userRepository->findByUsernameOrEmail($identifier);
        
        if (!$user) {
            $this->recordFailedAttempt();
            return false;
        }

        // Check if user is active
        if (!$user->isActive()) {
            return false;
        }

        // Verify password
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = Database::getInstance()->getConnection()->prepare($sql);
        $stmt->execute([$user->getId()]);
        $passwordHash = $stmt->fetchColumn();

        if (!User::verifyPassword($password, $passwordHash)) {
            $this->recordFailedAttempt();
            return false;
        }

        // Login successful
        $this->login($user, $remember);
        $this->clearFailedAttempts();
        
        return true;
    }

    /**
     * Login user
     * 
     * @param User $user
     * @param bool $remember
     */
    private function login(User $user, $remember = false)
    {
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Store user ID in session
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_role'] = $user->getRole();
        $_SESSION['login_time'] = time();
        
        // Update last login
        $this->userRepository->updateLastLogin($user->getId());
        
        // Set current user
        $this->currentUser = $user;
        
        // Remember me functionality (optional - not implemented in this version)
        if ($remember) {
            // Set remember token cookie (implementation needed)
        }
    }

    /**
     * Logout current user
     */
    public function logout()
    {
        $this->currentUser = null;
        
        // Clear session
        $_SESSION = [];
        
        // Destroy session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public function check()
    {
        return $this->currentUser !== null;
    }

    /**
     * Check if user is guest (not authenticated)
     * 
     * @return bool
     */
    public function guest()
    {
        return $this->currentUser === null;
    }

    /**
     * Get current authenticated user
     * 
     * @return User|null
     */
    public function user()
    {
        return $this->currentUser;
    }

    /**
     * Get current user ID
     * 
     * @return int|null
     */
    public function id()
    {
        return $this->currentUser ? $this->currentUser->getId() : null;
    }

    /**
     * Check if user has specific role
     * 
     * @param string|array $roles
     * @return bool
     */
    public function hasRole($roles)
    {
        if (!$this->check()) {
            return false;
        }
        
        return $this->currentUser->hasRole($roles);
    }

    /**
     * Check if user has permission
     * 
     * @param string $permission Format: "resource.action" (e.g., "patients.create")
     * @return bool
     */
    public function can($permission)
    {
        if (!$this->check()) {
            return false;
        }
        
        $role = $this->currentUser->getRole();
        $permissions = $this->config['roles'][$role]['permissions'] ?? [];
        
        // Admin has all permissions
        if (in_array('*', $permissions)) {
            return true;
        }
        
        return in_array($permission, $permissions);
    }

    /**
     * Check if user cannot perform action
     * 
     * @param string $permission
     * @return bool
     */
    public function cannot($permission)
    {
        return !$this->can($permission);
    }

    /**
     * Record failed login attempt
     */
    private function recordFailedAttempt()
    {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['first_attempt_time'] = time();
        }
        
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();
    }

    /**
     * Clear failed login attempts
     */
    private function clearFailedAttempts()
    {
        unset($_SESSION['login_attempts']);
        unset($_SESSION['first_attempt_time']);
        unset($_SESSION['last_attempt_time']);
    }

    /**
     * Check if user is locked out due to too many failed attempts
     * 
     * @return bool
     */
    public function isLockedOut()
    {
        if (!isset($_SESSION['login_attempts'])) {
            return false;
        }
        
        $maxAttempts = $this->config['security']['login_max_attempts'];
        $lockoutTime = $this->config['security']['lockout_time'];
        
        if ($_SESSION['login_attempts'] >= $maxAttempts) {
            $timeSinceLastAttempt = time() - $_SESSION['last_attempt_time'];
            
            if ($timeSinceLastAttempt < $lockoutTime) {
                return true;
            } else {
                // Lockout time has passed, clear attempts
                $this->clearFailedAttempts();
                return false;
            }
        }
        
        return false;
    }

    /**
     * Get remaining lockout time in seconds
     * 
     * @return int
     */
    public function getRemainingLockoutTime()
    {
        if (!$this->isLockedOut()) {
            return 0;
        }
        
        $lockoutTime = $this->config['security']['lockout_time'];
        $timeSinceLastAttempt = time() - $_SESSION['last_attempt_time'];
        
        return max(0, $lockoutTime - $timeSinceLastAttempt);
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
