<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Autoload.php';

use Core\Env;
use Core\Router;

Env::load(__DIR__ . '/../.env');

use App\Controllers\AuthController;
use App\Controllers\CriteriaController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;
use App\Controllers\SessionController;
use App\Controllers\SupplierController;
use App\Controllers\UserController;
use App\Middleware\AuthMiddleware;

$router = new Router();

$router->get('/', [DashboardController::class, 'index'], [AuthMiddleware::class]);
$router->get('/login', [AuthController::class, 'loginView']);
$router->post('/login', [AuthController::class, 'authenticate']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class]);

$router->get('/suppliers', [SupplierController::class, 'index'], [AuthMiddleware::class]);
$router->post('/suppliers', [SupplierController::class, 'store'], [AuthMiddleware::class]);
$router->post('/suppliers/update', [SupplierController::class, 'update'], [AuthMiddleware::class]);
$router->post('/suppliers/delete', [SupplierController::class, 'destroy'], [AuthMiddleware::class]);

$router->get('/criteria', [CriteriaController::class, 'index'], [AuthMiddleware::class]);
$router->post('/criteria', [CriteriaController::class, 'store'], [AuthMiddleware::class]);
$router->post('/criteria/update', [CriteriaController::class, 'update'], [AuthMiddleware::class]);
$router->post('/criteria/delete', [CriteriaController::class, 'destroy'], [AuthMiddleware::class]);

$router->get('/users', [UserController::class, 'index'], [AuthMiddleware::class]);
$router->post('/users', [UserController::class, 'store'], [AuthMiddleware::class]);
$router->post('/users/update', [UserController::class, 'update'], [AuthMiddleware::class]);
$router->post('/users/delete', [UserController::class, 'destroy'], [AuthMiddleware::class]);

$router->get('/profile', [ProfileController::class, 'index'], [AuthMiddleware::class]);
$router->post('/profile/update', [ProfileController::class, 'update'], [AuthMiddleware::class]);

$router->get('/sessions', [SessionController::class, 'index'], [AuthMiddleware::class]);
$router->get('/sessions/create', [SessionController::class, 'create'], [AuthMiddleware::class]);
$router->post('/sessions', [SessionController::class, 'store'], [AuthMiddleware::class]);
$router->get('/sessions/show', [SessionController::class, 'show'], [AuthMiddleware::class]);
$router->post('/sessions/save-matrix', [SessionController::class, 'saveMatrix'], [AuthMiddleware::class]);
$router->get('/sessions/result', [SessionController::class, 'calculate'], [AuthMiddleware::class]);
$router->post('/sessions/delete', [SessionController::class, 'destroy'], [AuthMiddleware::class]);
$router->post('/sessions/complete', [SessionController::class, 'complete'], [AuthMiddleware::class]);

$router->dispatch();
