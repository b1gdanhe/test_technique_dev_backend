<?php
const BASE_PATH  =  __DIR__ . '/../';

require BASE_PATH . 'vendor/autoload.php';
$app =   Core\App::getInstance();

$app->get('/user', fn() => print('bonjout'));
// $app->post('/register', fn() => print('bonjout'));
// $app->post('/login', fn() => print('bonjout'));
// $app->patch('/user/update', fn() => print('bonjout'));
