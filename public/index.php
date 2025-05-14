<?php

use Src\Controller\AuthController;
use Src\Controller\UserController;
use Src\Middleware\AuthMiddleware;



const BASE_PATH  =  __DIR__ . '/../';

require BASE_PATH . 'vendor/autoload.php';
require BASE_PATH . 'core/helpers.php';
$app =   Core\App::getInstance();
// Define routes
$app->post('/api/register', [AuthController::class, 'register']);
$app->post('/api/login', [AuthController::class, 'login']);

// Protected routes (require JWT authentication)
$app->put('/api/user/profile', [UserController::class, 'updateProfile', [AuthMiddleware::class]]);
$app->get('/api/user', [UserController::class, 'getUser', [AuthMiddleware::class]]);

// Run the application
$app->run();