<?php
/**
 * CSRF Protection Helper
 * 
 * Menyediakan fungsi untuk generate dan verifikasi CSRF token
 * untuk melindungi aplikasi dari Cross-Site Request Forgery attacks
 */
class Csrf
{
    /**
     * Generate atau ambil CSRF token dari session
     * 
     * @return string CSRF token
     */
    public static function generate(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verifikasi CSRF token
     * 
     * @param string $token Token yang akan diverifikasi
     * @return bool True jika valid, false jika tidak
     */
    public static function verify(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Generate HTML hidden input field untuk CSRF token
     * 
     * @return string HTML input field
     */
    public static function field(): string
    {
        $token = self::generate();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Verifikasi dan throw exception jika tidak valid
     * 
     * @param string|null $token Token yang akan diverifikasi
     * @throws Exception Jika token tidak valid
     */
    public static function verifyOrFail(?string $token): void
    {
        if (!self::verify($token ?? '')) {
            http_response_code(403);
            die('CSRF token verification failed. Request rejected for security reasons.');
        }
    }
    
    /**
     * Regenerate CSRF token (untuk keamanan ekstra setelah operasi sensitif)
     */
    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
