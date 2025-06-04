<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\MessagesController;
use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\ProjectController;
use App\Router;
use App\Template;

session_start();

$template = new Template(__DIR__ . '/../views');
$router = new Router($template);

$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/projects', [ProjectController::class, 'index']);
$router->get('/contact', [ContactController::class, 'index']);
$router->post('/contact', [ContactController::class, 'post']);
$router->get('/login', [LoginController::class, 'index']);
$router->post('/login', [LoginController::class, 'login']);
$router->post('/logout', [LoginController::class, 'logout']);
$router->get('/admin/dashboard', [DashboardController::class, 'index']);
$router->get('/admin/messages', [MessagesController::class, 'index']);
$router->post('/admin/messages/{id}/toggle-read', [MessagesController::class, 'toggleRead']);
$router->post('/admin/messages/{id}/delete', [MessagesController::class, 'delete']);

// Dispatch current request
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
