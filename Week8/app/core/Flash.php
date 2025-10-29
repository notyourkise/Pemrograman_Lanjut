<?php

/**
 * Week 8: Flash Message Helper
 * Store and retrieve one-time flash messages
 */

class Flash
{
    /**
     * Set a flash message
     * 
     * @param string $key Message key (success, error, warning, info)
     * @param string $message Message text
     */
    public static function set($key, $message)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Get and remove a flash message
     * 
     * @param string $key
     * @return string|null
     */
    public static function get($key)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        
        return null;
    }

    /**
     * Check if flash message exists
     * 
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        return isset($_SESSION['flash'][$key]);
    }

    /**
     * Display flash message HTML
     * 
     * @param string $key
     * @return string
     */
    public static function display($key = null)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $html = '';
        $keys = $key ? [$key] : ['success', 'error', 'warning', 'info'];
        
        foreach ($keys as $k) {
            if ($message = self::get($k)) {
                $class = self::getAlertClass($k);
                $html .= "<div class='alert alert-{$class} alert-dismissible fade show' role='alert'>";
                $html .= htmlspecialchars($message);
                $html .= "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
                $html .= "</div>";
            }
        }
        
        return $html;
    }

    /**
     * Get Bootstrap alert class for message type
     * 
     * @param string $key
     * @return string
     */
    private static function getAlertClass($key)
    {
        $classes = [
            'success' => 'success',
            'error' => 'danger',
            'warning' => 'warning',
            'info' => 'info'
        ];
        
        return $classes[$key] ?? 'info';
    }
}
