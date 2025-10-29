<?php

/**
 * Week 8: Front Controller
 * Entry point for the application with routing
 */

// Start output buffering
ob_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Load autoloader
require_once __DIR__ . '/../app/core/Autoloader.php';
Autoloader::register();

// Load URL helper
require_once __DIR__ . '/../app/helpers/Url.php';

// Get request URI and method
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove query string from URI
$uri = parse_url($requestUri, PHP_URL_PATH);

// Remove base path if exists (for subdirectory installations)
$basePath = '/MATERI-ASDOS/Week8/public';
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// Ensure URI starts with /
if (empty($uri) || $uri[0] !== '/') {
    $uri = '/' . $uri;
}

// Route dispatcher
try {
    // Define routes
    $routes = [
        // Authentication routes
        'GET /login' => ['AuthController', 'showLogin'],
        'POST /login' => ['AuthController', 'login'],
        'POST /logout' => ['AuthController', 'logout'],
        'GET /dashboard' => ['AuthController', 'dashboard'],
        
        // Users Management routes (Admin only)
        'GET /users' => ['UsersController', 'index'],
        'GET /users/create' => ['UsersController', 'create'],
        'POST /users/store' => ['UsersController', 'store'],
        
        // Appointments routes - Doctor
        'GET /appointments/doctor' => ['AppointmentsController', 'doctorIndex'],
        
        // Appointments routes - Receptionist
        'GET /appointments/receptionist' => ['AppointmentsController', 'receptionistIndex'],
        'GET /appointments/receptionist/create' => ['AppointmentsController', 'receptionistCreate'],
        'POST /appointments/receptionist/store' => ['AppointmentsController', 'receptionistStore'],
        
        // Home route
        'GET /' => function() {
            $auth = Auth::getInstance();
            if ($auth->check()) {
                header('Location: ' . url('dashboard'));
            } else {
                header('Location: ' . url('login'));
            }
            exit;
        },
        
        // Patients routes (to be implemented - copy from Week 7)
        'GET /patients' => function() {
            echo "Patients list - To be implemented (copy from Week 7)";
        },
        
        // Appointments routes (to be implemented - copy from Week 7)
        'GET /appointments' => function() {
            echo "Appointments list - To be implemented (copy from Week 7)";
        },
    ];

    // Build route key
    $routeKey = $requestMethod . ' ' . $uri;
    
    // Check for dynamic routes with parameters
    $matched = false;
    
    // Try exact match first
    if (isset($routes[$routeKey])) {
        $handler = $routes[$routeKey];
        
        if (is_callable($handler)) {
            $handler();
        } elseif (is_array($handler)) {
            [$controllerName, $method] = $handler;
            $controller = new $controllerName();
            $controller->$method();
        }
        $matched = true;
    } 
    // Try pattern matching for routes with IDs
    else {
        // Handle /users/edit/{id}
        if (preg_match('#^GET /users/edit/(\d+)$#', $routeKey, $matches)) {
            $controller = new UsersController();
            $controller->edit($matches[1]);
            $matched = true;
        }
        // Handle /users/update/{id}
        elseif (preg_match('#^POST /users/update/(\d+)$#', $routeKey, $matches)) {
            $controller = new UsersController();
            $controller->update($matches[1]);
            $matched = true;
        }
        // Handle /users/delete/{id}
        elseif (preg_match('#^POST /users/delete/(\d+)$#', $routeKey, $matches)) {
            $controller = new UsersController();
            $controller->delete($matches[1]);
            $matched = true;
        }
        // Handle /appointments/doctor/approve/{id}
        elseif (preg_match('#^POST /appointments/doctor/approve/(\d+)$#', $routeKey, $matches)) {
            $controller = new AppointmentsController();
            $controller->doctorApprove($matches[1]);
            $matched = true;
        }
        // Handle /appointments/doctor/reject/{id}
        elseif (preg_match('#^POST /appointments/doctor/reject/(\d+)$#', $routeKey, $matches)) {
            $controller = new AppointmentsController();
            $controller->doctorReject($matches[1]);
            $matched = true;
        }
        // Handle /appointments/doctor/request-cancel/{id}
        elseif (preg_match('#^POST /appointments/doctor/request-cancel/(\d+)$#', $routeKey, $matches)) {
            $controller = new AppointmentsController();
            $controller->doctorRequestCancel($matches[1]);
            $matched = true;
        }
        // Handle /appointments/doctor/start/{id}
        elseif (preg_match('#^POST /appointments/doctor/start/(\d+)$#', $routeKey, $matches)) {
            $controller = new AppointmentsController();
            $controller->doctorStartAppointment($matches[1]);
            $matched = true;
        }
        // Handle /appointments/doctor/complete/{id}
        elseif (preg_match('#^POST /appointments/doctor/complete/(\d+)$#', $routeKey, $matches)) {
            $controller = new AppointmentsController();
            $controller->doctorCompleteAppointment($matches[1]);
            $matched = true;
        }
        // Handle /appointments/receptionist/approve-cancel/{id}
        elseif (preg_match('#^POST /appointments/receptionist/approve-cancel/(\d+)$#', $routeKey, $matches)) {
            $controller = new AppointmentsController();
            $controller->receptionistApproveCancel($matches[1]);
            $matched = true;
        }
        // Handle /appointments/receptionist/reject-cancel/{id}
        elseif (preg_match('#^POST /appointments/receptionist/reject-cancel/(\d+)$#', $routeKey, $matches)) {
            $controller = new AppointmentsController();
            $controller->receptionistRejectCancel($matches[1]);
            $matched = true;
        }
    }
    
    // If no route matched, show 404
    if (!$matched) {
        // 404 Not Found
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>Route: <code>{$routeKey}</code> not found</p>";
        echo "<p>Request URI: <code>{$requestUri}</code></p>";
        echo "<p>Parsed URI: <code>{$uri}</code></p>";
        echo "<p>Base Path: <code>{$basePath}</code></p>";
        echo "<hr>";
        echo "<h3>Available Routes:</h3>";
        echo "<ul>";
        foreach (array_keys($routes) as $route) {
            echo "<li><code>{$route}</code></li>";
        }
        echo "</ul>";
        echo "<p><a href='" . url('/') . "'>Go to Home</a></p>";
    }
    
} catch (Exception $e) {
    // Error handling
    http_response_code(500);
    echo "<h1>500 - Internal Server Error</h1>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

// End output buffering
ob_end_flush();
