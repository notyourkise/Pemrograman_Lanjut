<?php

/**
 * Week 8: Auth Middleware
 * Middleware to protect routes with authentication and authorization
 */

class AuthMiddleware
{
    private $auth;

    public function __construct()
    {
        $this->auth = Auth::getInstance();
    }

    /**
     * Require authentication
     * Redirect to login if not authenticated
     * 
     * @param string $redirectTo URL to redirect after login
     */
    public function requireAuth($redirectTo = null)
    {
        if (!$this->auth->check()) {
            Flash::set('error', 'Please login to continue');
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            $redirectTo = $redirectTo ?? url('login');
            header("Location: {$redirectTo}");
            exit;
        }
    }

    /**
     * Require guest (not authenticated)
     * Redirect to dashboard if authenticated
     * 
     * @param string $redirectTo
     */
    public function requireGuest($redirectTo = null)
    {
        if ($this->auth->check()) {
            $redirectTo = $redirectTo ?? url('dashboard');
            header("Location: {$redirectTo}");
            exit;
        }
    }

    /**
     * Require specific role
     * 
     * @param string|array $roles
     * @param string $redirectTo
     */
    public function requireRole($roles, $redirectTo = null)
    {
        $this->requireAuth();
        
        if (!$this->auth->hasRole($roles)) {
            Flash::set('error', 'You do not have permission to access this page');
            $redirectTo = $redirectTo ?? url('dashboard');
            header("Location: {$redirectTo}");
            exit;
        }
    }

    /**
     * Require specific permission
     * 
     * @param string $permission
     * @param string $redirectTo
     */
    public function requirePermission($permission, $redirectTo = null)
    {
        $this->requireAuth();
        
        if (!$this->auth->can($permission)) {
            Flash::set('error', 'You do not have permission to perform this action');
            $redirectTo = $redirectTo ?? url('dashboard');
            header("Location: {$redirectTo}");
            exit;
        }
    }

    /**
     * Require admin role
     * 
     * @param string $redirectTo
     */
    public function requireAdmin($redirectTo = null)
    {
        $this->requireRole('admin', $redirectTo);
    }

    /**
     * Require doctor role
     * 
     * @param string $redirectTo
     */
    public function requireDoctor($redirectTo = null)
    {
        $this->requireRole('doctor', $redirectTo);
    }

    /**
     * Require receptionist role
     * 
     * @param string $redirectTo
     */
    public function requireReceptionist($redirectTo = '/dashboard')
    {
        $this->requireRole('receptionist', $redirectTo);
    }
}
