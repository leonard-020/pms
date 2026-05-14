<?php
/**
 * ============================================================
 * Parish Management System — Entry Point
 * ============================================================
 */

error_reporting(E_ALL);
ini_set('display_errors', '0'); // Errors are handled by our ErrorHandler

// -------------------------------------------------------
// Autoloader (PSR-4 compliant)
// -------------------------------------------------------
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// -------------------------------------------------------
// Load dependencies
// -------------------------------------------------------
require_once dirname(__DIR__) . '/app/Core/ErrorHandler.php';
require_once dirname(__DIR__) . '/app/Core/Database.php';
require_once dirname(__DIR__) . '/app/Core/Session.php';
require_once dirname(__DIR__) . '/app/Core/CSRF.php';
require_once dirname(__DIR__) . '/app/Core/Request.php';
require_once dirname(__DIR__) . '/app/Core/Response.php';
require_once dirname(__DIR__) . '/app/Core/Validator.php';
require_once dirname(__DIR__) . '/app/Core/Cache.php';
require_once dirname(__DIR__) . '/app/Core/Router.php';
require_once dirname(__DIR__) . '/app/Core/Controller.php';
require_once dirname(__DIR__) . '/app/Core/Model.php';

// -------------------------------------------------------
// Bootstrap
// -------------------------------------------------------
App\Core\ErrorHandler::register();
App\Core\Session::start();
App\Core\Cache::init();

// -------------------------------------------------------
// Handle PUT/DELETE via _method field
// -------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
    $_SERVER['REQUEST_METHOD'] = strtoupper($_POST['_method']);
}

// -------------------------------------------------------
// Route definitions
// -------------------------------------------------------
 $router = new App\Core\Router();

// --- Root redirect ---
 $router->get('/', function() {
    if (App\Core\Session::has('user')) {
        App\Core\Response::redirect('/dashboard');
    }
    App\Core\Response::redirect('/login');
});

// --- Guest routes ---
 $router->get('/login', 'AuthController@showLogin', ['GuestMiddleware']);
 $router->post('/login', 'AuthController@login', ['GuestMiddleware']);
 $router->post('/logout', 'AuthController@logout', ['AuthMiddleware']);

// --- Authenticated routes ---
 $router->get('/dashboard', 'DashboardController@index', ['AuthMiddleware']);

// Members
 $router->get('/members', 'MemberController@index', ['AuthMiddleware', 'RBACMiddleware:members.view']);
 $router->get('/members/create', 'MemberController@create', ['AuthMiddleware', 'RBACMiddleware:members.create']);
 $router->post('/members', 'MemberController@store', ['AuthMiddleware', 'RBACMiddleware:members.create']);
 $router->get('/members/{id}', 'MemberController@show', ['AuthMiddleware', 'RBACMiddleware:members.view']);
 $router->get('/members/{id}/edit', 'MemberController@edit', ['AuthMiddleware', 'RBACMiddleware:members.update']);
 $router->post('/members/{id}', 'MemberController@update', ['AuthMiddleware', 'RBACMiddleware:members.update']);

// Finance
 $router->get('/finance', 'FinanceController@index', ['AuthMiddleware', 'RBACMiddleware:finance.view']);
 $router->get('/finance/create', 'FinanceController@create', ['AuthMiddleware', 'RBACMiddleware:finance.create']);
 $router->post('/finance', 'FinanceController@store', ['AuthMiddleware', 'RBACMiddleware:finance.create']);
 $router->post('/finance/{id}/approve', 'FinanceController@approve', ['AuthMiddleware', 'RBACMiddleware:finance.approve']);
 $router->post('/finance/{id}/reject', 'FinanceController@reject', ['AuthMiddleware', 'RBACMiddleware:finance.approve']);

// Sacraments
 $router->get('/sacraments', 'SacramentController@index', ['AuthMiddleware', 'RBACMiddleware:sacraments.view']);
 $router->get('/sacraments/create', 'SacramentController@create', ['AuthMiddleware', 'RBACMiddleware:sacraments.create']);
 $router->post('/sacraments', 'SacramentController@store', ['AuthMiddleware', 'RBACMiddleware:sacraments.create']);

// Events
 $router->get('/events', 'EventController@index', ['AuthMiddleware', 'RBACMiddleware:events.view']);
 $router->get('/events/create', 'EventController@create', ['AuthMiddleware', 'RBACMiddleware:events.create']);
 $router->post('/events', 'EventController@store', ['AuthMiddleware', 'RBACMiddleware:events.create']);

// Groups
 $router->get('/groups', 'GroupController@index', ['AuthMiddleware', 'RBACMiddleware:groups.view']);
 $router->get('/groups/create', 'GroupController@create', ['AuthMiddleware', 'RBACMiddleware:groups.create']);
 $router->post('/groups', 'GroupController@store', ['AuthMiddleware', 'RBACMiddleware:groups.create']);
 $router->get('/groups/{id}', 'GroupController@show', ['AuthMiddleware', 'RBACMiddleware:groups.view']);

// Users (Super Admin only)
 $router->get('/users', 'UserController@index', ['AuthMiddleware', 'RBACMiddleware:users.view']);
 $router->get('/users/create', 'UserController@create', ['AuthMiddleware', 'RBACMiddleware:users.create']);
 $router->post('/users', 'UserController@store', ['AuthMiddleware', 'RBACMiddleware:users.create']);
 $router->post('/users/{id}/activate', 'UserController@activate', ['AuthMiddleware', 'RBACMiddleware:users.activate']);
 $router->post('/users/{id}/deactivate', 'UserController@deactivate', ['AuthMiddleware', 'RBACMiddleware:users.deactivate']);

// Audit Logs (Super Admin + Auditor only)
 $router->get('/audit-logs', 'AuditLogController@index', ['AuthMiddleware', 'RBACMiddleware:audit_logs.view']);

// Profile (all authenticated users)
 $router->get('/profile', 'ProfileController@index', ['AuthMiddleware', 'RBACMiddleware:profile.view']);
 $router->post('/profile', 'ProfileController@updateProfile', ['AuthMiddleware', 'RBACMiddleware:profile.update']);

// -------------------------------------------------------
// Dispatch
// -------------------------------------------------------
// Calculate the base path (e.g., /pms/public) so routes work in subfolders
//  $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
//  $uri = '/' . trim(substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen($basePath)), '/') ?: '/';
//  $method = $_SERVER['REQUEST_METHOD'];

//  $router->dispatch($uri, $method);

// -------------------------------------------------------
// Dispatch
// -------------------------------------------------------
// Define base URL so all links and redirects work in subfolders (e.g., /pms/public)
 $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
define('BASE_URL', $basePath);

 $uri = '/' . trim(substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen($basePath)), '/') ?: '/';
 $method = $_SERVER['REQUEST_METHOD'];

 $router->dispatch($uri, $method);