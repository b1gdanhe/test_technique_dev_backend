<?php
const BASE_PATH  =  __DIR__ . '/../';

require BASE_PATH . 'vendor/autoload.php';
$app =   Core\App::getInstance();
// Define routes
$app->post('/api/register', [AuthController::class, 'register']);
$app->post('/api/login', [AuthController::class, 'login']);

// Protected routes (require JWT authentication)
$app->put('/api/user/profile', [UserController::class, 'updateProfile', [AuthMiddleware::class]]);
$app->get('/api/user/profile', [UserController::class, 'getProfile', [AuthMiddleware::class]]);

// Run the application
$app->run();

// $app->post('/register', fn() => print('bonjout'));
// $app->post('/login', fn() => print('bonjout'));
// $app->patch('/user/update', fn() => print('bonjout'));
