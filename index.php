<?php
require_once __DIR__ . '/Interfaces/UserRepositoryInterface.php';
require_once __DIR__ . '/Repositories/JsonUserRepository.php';
require_once __DIR__ . '/Repositories/MysqlUserRepository.php';
require_once __DIR__ . '/Services/UserService.php';

$chosen_db = parse_ini_file('.env');

if ($chosen_db['DB_SOURCE'] === 'mysql') {
    $db = new MysqlUserRepository($chosen_db);
} elseif ($chosen_db['DB_SOURCE'] === 'json') {
    $db = new JsonUserRepository();
}

$userService = new UserService($db);

if ($argc < 2) {
    echo 'Укажите команду. Показать список команд: php index.php help';
    exit();
}
switch ($argv[1]) {
    case 'database':
        $userService->changeDb($argv[2]);
        break;
    case 'show':
        $userService->showUsers();
        break;
    case 'add':
        $userService->add($argv[2] ?? null, $argv[3] ?? null, $argv[4] ?? null);
        break;
    case 'delete':
        if (isset($argv[2])) {
            $userService->delete($argv[2]);
            break;
        } else {
            echo "Укажите Id";
            exit();
        }
    case 'help':
        echo "Показать пользователей: php index.php show \n";
        echo "Выбрать базу данных: php index.php database [mysql или json] \n";
        echo "Добавить пользователя: php index.php add [имя], [фамилия], [email] \n";
        echo "Удалить пользователя: php index.php delete [id] \n";
        break;
    default:
        echo "Неизвестная команда, введите: php index.php help \n";
}












