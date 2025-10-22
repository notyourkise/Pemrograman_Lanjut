<?php
declare(strict_types=1);

// Start session untuk CSRF token
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load core files
require __DIR__ . '/../app/core/Autoloader.php';
require __DIR__ . '/../app/core/Controller.php';
require __DIR__ . '/../app/core/Database.php';
require __DIR__ . '/../app/core/Flash.php';
require __DIR__ . '/../app/core/Paginator.php';

// Load helper files (Week 7 - Security)
require __DIR__ . '/../app/helpers/Csrf.php';
require __DIR__ . '/../app/helpers/Validator.php';
require __DIR__ . '/../app/helpers/Sanitizer.php';

$c = $_GET['c'] ?? 'patients';
$a = $_GET['a'] ?? 'index';
$controllerMap = [ 'patients' => PatientsController::class, 'appointments' => AppointmentsController::class ];
if (!isset($controllerMap[$c])) { http_response_code(404); echo 'Controller not found'; exit; }
$controller = new $controllerMap[$c]();
if (!method_exists($controller, $a)) { http_response_code(404); echo 'Action not found'; exit; }
$controller->{$a}();
