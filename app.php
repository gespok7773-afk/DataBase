<?php
require_once __DIR__ . '/Interfaces/UserRepositoryInterface.php';
require_once __DIR__ . '/Repositories/JsonUserRepository.php';
require_once __DIR__ . '/Repositories/MysqlUserRepository.php';
require_once __DIR__ . '/Services/UserService.php';
require_once __DIR__ . '/Controllers/HttpController.php';
require_once __DIR__ . '/Controllers/ConsoleController.php';

$chosen_db = parse_ini_file('.env');

if ($chosen_db['DB_SOURCE'] === 'mysql') {
    $db = new MysqlUserRepository($chosen_db);
} elseif ($chosen_db['DB_SOURCE'] === 'json') {
    $db = new JsonUserRepository();
}

$userService = new UserService($db);

if (isset($_SERVER['HTTP_HOST'])) {
    $controller = new HttpController($userService);
}else{
    $controller = new ConsoleController($userService);
}
$controller->request();