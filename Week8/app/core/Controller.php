<?php

/**
 * Week 8: Controller Base Class
 * Base controller with authentication and view rendering
 */

class Controller
{
    protected $auth;
    protected $config;

    public function __construct()
    {
        $this->auth = Auth::getInstance();
        $this->config = require __DIR__ . '/../config.php';
    }

    /**
     * Render a view file
     * 
     * @param string $view View file path (e.g., 'patients/index')
     * @param array $data Data to pass to the view
     */
    protected function view($view, $data = [])
    {
        extract($data);
        
        $viewFile = __DIR__ . "/../views/{$view}.php";
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: {$view}");
        }
    }

    /**
     * Redirect to another URL
     * 
     * @param string $url
     */
    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Return JSON response
     * 
     * @param mixed $data
     * @param int $statusCode
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Check if user is authenticated
     * Redirect to login if not
     */
    protected function requireAuth()
    {
        if (!$this->auth->check()) {
            Flash::set('error', 'Please login to continue');
            $this->redirect('/login');
        }
    }

    /**
     * Check if user has specific role
     * 
     * @param string|array $roles
     */
    protected function requireRole($roles)
    {
        $this->requireAuth();
        
        if (!$this->auth->hasRole($roles)) {
            Flash::set('error', 'You do not have permission to access this page');
            $this->redirect('/dashboard');
        }
    }

    /**
     * Check if user has specific permission
     * 
     * @param string $permission
     */
    protected function requirePermission($permission)
    {
        $this->requireAuth();
        
        if (!$this->auth->can($permission)) {
            Flash::set('error', 'You do not have permission to perform this action');
            $this->redirect('/dashboard');
        }
    }
}
