<?php
declare(strict_types=1);

require __DIR__ . '/../app/core/Autoloader.php';
require __DIR__ . '/../app/core/Controller.php';
require __DIR__ . '/../app/core/Database.php';

// Very simple router: ?c=patients&a=index
$c = $_GET['c'] ?? 'patients';
$a = $_GET['a'] ?? 'index';

// Map controller name to class
$controllerMap = [
    'patients' => PatientsController::class,
];

if (!isset($controllerMap[$c])) {
    http_response_code(404);
    echo 'Controller not found';
    exit;
}

$controller = new $controllerMap[$c]();
if (!method_exists($controller, $a)) {
    http_response_code(404);
    echo 'Action not found';
    exit;
}

$controller->{$a}();
