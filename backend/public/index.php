<?php

declare(strict_types=1);

// Autoload (Simple implementation since we can't run composer install potentially)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Config\Database;
use App\Core\Router;
use App\Controller\CampaignController;
use App\Controller\UserController;

// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Initialize App
$db = new Database();
$db->initialize(); // Ensure tables exist

$router = new Router();

// Dependencies
$campaignController = new CampaignController($db);
$userController = new UserController($db);

// Routes
// Campaigns
$router->get('/api/campaigns', [$campaignController, 'index']);
$router->post('/api/campaigns', [$campaignController, 'create']);
$router->put('/api/campaigns/{id}', [$campaignController, 'update']);
$router->delete('/api/campaigns/{id}', [$campaignController, 'delete']);

// Users
$router->get('/api/users', [$userController, 'index']);
$router->post('/api/users', [$userController, 'create']);

// Dispatch
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
