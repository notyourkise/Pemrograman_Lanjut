<?php

/**
 * Week 8: Authentication Controller
 * Handles login, register, and logout operations
 */

class AuthController extends Controller
{
    private $userRepository;
    private $middleware;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
        $this->middleware = new AuthMiddleware();
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        // Redirect if already logged in
        $this->middleware->requireGuest();
        
        $this->view('auth/login', [
            'title' => 'Login'
        ]);
    }

    /**
     * Process login
     */
    public function login()
    {
        $this->middleware->requireGuest();

        // Validate input
        if (empty($_POST['identifier']) || empty($_POST['password'])) {
            Flash::set('error', 'Please provide username/email and password');
            $this->redirect(url('login'));
        }

        $identifier = trim($_POST['identifier']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']);

        // Check if locked out
        if ($this->auth->isLockedOut()) {
            $remainingTime = ceil($this->auth->getRemainingLockoutTime() / 60);
            Flash::set('error', "Too many failed attempts. Please try again in {$remainingTime} minutes");
            $this->redirect(url('login'));
        }

        // Attempt login
        if ($this->auth->attempt($identifier, $password, $remember)) {
            // Login successful
            Flash::set('success', 'Welcome back, ' . $this->auth->user()->getFullName());
            
            // Redirect to intended URL or dashboard
            $redirectTo = $_SESSION['intended_url'] ?? url('dashboard');
            unset($_SESSION['intended_url']);
            
            $this->redirect($redirectTo);
        } else {
            // Login failed
            Flash::set('error', 'Invalid credentials. Please try again');
            $this->redirect(url('login'));
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->auth->logout();
        Flash::set('success', 'You have been logged out');
        $this->redirect(url('login'));
    }

    /**
     * Show dashboard (role-based)
     */
    public function dashboard()
    {
        $this->middleware->requireAuth();
        
        $user = $this->auth->user();
        $stats = $this->getDashboardStats();
        
        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'user' => $user,
            'stats' => $stats
        ]);
    }

    /**
     * Get dashboard statistics based on role
     * 
     * @return array
     */
    private function getDashboardStats()
    {
        $db = Database::getInstance()->getConnection();
        $stats = [];

        try {
            // Total patients
            $stmt = $db->query("SELECT COUNT(*) FROM patients WHERE deleted_at IS NULL");
            $stats['total_patients'] = $stmt->fetchColumn();
        } catch (Exception $e) {
            // Table might not have deleted_at column or table doesn't exist
            try {
                $stmt = $db->query("SELECT COUNT(*) FROM patients");
                $stats['total_patients'] = $stmt->fetchColumn();
            } catch (Exception $e) {
                $stats['total_patients'] = 0;
            }
        }

        try {
            // Total appointments
            $stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE deleted_at IS NULL");
            $stats['total_appointments'] = $stmt->fetchColumn();
        } catch (Exception $e) {
            try {
                $stmt = $db->query("SELECT COUNT(*) FROM appointments");
                $stats['total_appointments'] = $stmt->fetchColumn();
            } catch (Exception $e) {
                $stats['total_appointments'] = 0;
            }
        }

        try {
            // Scheduled appointments
            $stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'scheduled' AND deleted_at IS NULL");
            $stats['scheduled_appointments'] = $stmt->fetchColumn();
        } catch (Exception $e) {
            try {
                $stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'scheduled'");
                $stats['scheduled_appointments'] = $stmt->fetchColumn();
            } catch (Exception $e) {
                $stats['scheduled_appointments'] = 0;
            }
        }

        try {
            // Total doctors
            $stmt = $db->query("SELECT COUNT(*) FROM doctors");
            $stats['total_doctors'] = $stmt->fetchColumn();
        } catch (Exception $e) {
            $stats['total_doctors'] = 0;
        }

        // Role-specific stats
        if ($this->auth->hasRole('admin')) {
            $stats['user_counts'] = $this->userRepository->countByRole();
        }

        return $stats;
    }
}
