<?php

/**
 * Week 8: Users Management Controller
 * Admin can create Doctor and Receptionist accounts
 */

class UsersController extends Controller
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
     * List all users (Admin only)
     */
    public function index()
    {
        $this->middleware->requireAdmin();
        
        $users = $this->userRepository->findAll();
        
        $this->view('users/index', [
            'title' => 'Users Management',
            'users' => $users
        ]);
    }

    /**
     * Show create user form (Admin only)
     */
    public function create()
    {
        $this->middleware->requireAdmin();
        
        $this->view('users/create', [
            'title' => 'Create User'
        ]);
    }

    /**
     * Store new user (Admin only)
     */
    public function store()
    {
        $this->middleware->requireAdmin();

        // Validate input
        $errors = [];

        if (empty($_POST['username'])) {
            $errors['username'] = 'Username is required';
        } elseif (strlen($_POST['username']) < 3) {
            $errors['username'] = 'Username must be at least 3 characters';
        } elseif ($this->userRepository->usernameExists($_POST['username'])) {
            $errors['username'] = 'Username already exists';
        }

        if (empty($_POST['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } elseif ($this->userRepository->emailExists($_POST['email'])) {
            $errors['email'] = 'Email already exists';
        }

        if (empty($_POST['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($_POST['password']) < $this->config['security']['password_min_length']) {
            $errors['password'] = 'Password must be at least ' . $this->config['security']['password_min_length'] . ' characters';
        }

        if (empty($_POST['full_name'])) {
            $errors['full_name'] = 'Full name is required';
        }

        if (empty($_POST['role'])) {
            $errors['role'] = 'Role is required';
        } elseif (!in_array($_POST['role'], ['doctor', 'receptionist'])) {
            $errors['role'] = 'Invalid role. Only Doctor or Receptionist allowed';
        }

        // If there are errors, redirect back
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect(url('users/create'));
        }

        // Create user
        try {
            $user = $this->userRepository->create([
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'],
                'full_name' => trim($_POST['full_name']),
                'role' => $_POST['role'],
                'is_active' => isset($_POST['is_active']) ? true : false
            ]);

            Flash::set('success', ucfirst($_POST['role']) . ' account created successfully for ' . $user->getFullName());
            $this->redirect(url('users'));
        } catch (Exception $e) {
            Flash::set('error', 'Failed to create user. Please try again');
            $this->redirect(url('users/create'));
        }
    }

    /**
     * Show edit user form (Admin only)
     */
    public function edit($id)
    {
        $this->middleware->requireAdmin();
        
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            Flash::set('error', 'User not found');
            $this->redirect(url('users'));
        }

        $this->view('users/edit', [
            'title' => 'Edit User',
            'user' => $user
        ]);
    }

    /**
     * Update user (Admin only)
     */
    public function update($id)
    {
        $this->middleware->requireAdmin();
        
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            Flash::set('error', 'User not found');
            $this->redirect(url('users'));
        }

        // Validate input
        $errors = [];

        if (empty($_POST['full_name'])) {
            $errors['full_name'] = 'Full name is required';
        }

        if (empty($_POST['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } elseif ($this->userRepository->emailExists($_POST['email'], $id)) {
            $errors['email'] = 'Email already exists';
        }

        // If there are errors, redirect back
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect(url('users/edit/' . $id));
        }

        // Update user
        try {
            $updateData = [
                'email' => trim($_POST['email']),
                'full_name' => trim($_POST['full_name']),
                'is_active' => isset($_POST['is_active']) ? true : false
            ];

            // Update password if provided
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < $this->config['security']['password_min_length']) {
                    Flash::set('error', 'Password must be at least ' . $this->config['security']['password_min_length'] . ' characters');
                    $this->redirect(url('users/edit/' . $id));
                }
                $updateData['password'] = $_POST['password'];
            }

            $this->userRepository->update($id, $updateData);

            Flash::set('success', 'User updated successfully');
            $this->redirect(url('users'));
        } catch (Exception $e) {
            Flash::set('error', 'Failed to update user. Please try again');
            $this->redirect(url('users/edit/' . $id));
        }
    }

    /**
     * Delete user (Admin only)
     */
    public function delete($id)
    {
        $this->middleware->requireAdmin();
        
        try {
            $this->userRepository->delete($id);
            Flash::set('success', 'User deleted successfully');
        } catch (Exception $e) {
            Flash::set('error', 'Failed to delete user');
        }
        
        $this->redirect(url('users'));
    }
}
