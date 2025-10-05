<?php
declare(strict_types=1);
require __DIR__ . '/../app/core/Autoloader.php';
require __DIR__ . '/../app/core/Controller.php';
require __DIR__ . '/../app/core/Database.php';
require __DIR__ . '/../app/core/Flash.php';
require __DIR__ . '/../app/core/Paginator.php';

$c = $_GET['c'] ?? 'patients';
$a = $_GET['a'] ?? 'index';
$controllerMap = [ 'patients' => PatientsController::class, 'appointments' => AppointmentsController::class ];
if (!isset($controllerMap[$c])) { http_response_code(404); echo 'Controller not found'; exit; }
$controller = new $controllerMap[$c]();
if (!method_exists($controller, $a)) { http_response_code(404); echo 'Action not found'; exit; }
$controller->{$a}();
