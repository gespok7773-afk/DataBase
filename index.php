<?php
$chosen_db = parse_ini_file('.env');
function get_data()
{
    global $chosen_db;
    if ($chosen_db["DB_SOURCE"] === "json") {
        return json_decode(file_get_contents('users.json'), true);
    }
    if ($chosen_db["DB_SOURCE"] === "mysql") {
        $pdo = new PDO("mysql:host={$chosen_db['DB_HOST']};dbname={$chosen_db['DB_NAME']};charset=utf8", $chosen_db['DB_USER'], $chosen_db['DB_PASSWORD']);

        return $pdo->query("SELECT * FROM users")->fetchAll();
    }
}

function put_data($users, $name = null, $surname = null, $email = null, $id=null, $action=null)
{
    global $chosen_db;
    if ($chosen_db["DB_SOURCE"] == "json") {
        return file_put_contents('users.json', json_encode($users, JSON_UNESCAPED_UNICODE));
    }
    if ($chosen_db["DB_SOURCE"] === "mysql") {
        $pdo = new PDO("mysql:host={$chosen_db['DB_HOST']};dbname={$chosen_db['DB_NAME']};charset=utf8", $chosen_db['DB_USER'], $chosen_db['DB_PASSWORD']);
        if($action==='add'){
            $stmt = $pdo->prepare("INSERT INTO users (name, surname, email) VALUES (?, ?, ?)");
            $stmt->execute([$name, $surname, $email]);
        }
        if($action==='delete'){
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
        }

    }
}

function showUsers()
{
    $users = get_data();
    echo "Текущий список пользователей: \n";
    foreach ($users as $user) {
        echo $user['id'] . '. Имя: ' . $user['name'] . ', Фамилия:' . $user['surname'] . ', E-mail:' . $user['email'] . "\n";
    }
}

function addUser($name = null, $surname = null, $email = null)
{
    if (!isset($name)) {
        $names = ['Михаил', 'Александр', 'Артём', 'Тимофей', 'Матвей', 'Иван', 'Петр', 'Алексей', 'Дмитрий', 'Сергей'];
        $name = $names[array_rand($names)];
    }

    if (!isset($surname)) {
        $surnames = ['Александров', 'Алексеев', 'Михайлов', 'Яковлев', 'Петров', 'Кузнецов', 'Попов', 'Бондарев', 'Мельников', 'Рыбаков'];
        $surname = $surnames[array_rand($surnames)];
    }

    $translit = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',
        'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
        'ы' => 'y', 'ь' => '', 'ъ' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
    ];


    if (!isset($email)) {
        $email = strtr(strtolower($surname), $translit) . '@mail.ru';
    }
    $users = get_data();
    $ids = array_column($users, 'id');
    $id = empty($ids) ? 1 : max($ids) + 1;

    foreach ($users as $user) {
        if ($user['email'] == $email) {
            echo 'Такой пользователь уже существует';
            return;
        }
    }
    $users[] = ['id' => $id, 'name' => $name, 'surname' => $surname, 'email' => $email];
    echo 'Пользователь ' . $name . ' ' . $surname . ' добавлен';


    put_data($users, $name, $surname, $email, $id, 'add');
}

function deleteUser($id)
{
    $users = get_data();
    $found = false;
    foreach ($users as $key => $user) {
        if ($user['id'] == $id) {
            unset($users[$key]);
            $found = true;
        }
    }
    if ($found) {
        put_data($users, null, null, null, $id, 'delete');
        echo "Пользователь с ID: " . $id . " удален\n";
    } else {
        echo 'Пользователя c таким Id не существует';
    }
}
function change_db($base_name)
{
    if($base_name === "mysql"){
        $env = file_get_contents('.env');
        $env = preg_replace('/DB_SOURCE=.*/', "DB_SOURCE=mysql", $env);
        file_put_contents('.env', $env);
        echo 'Подключена база mysql';
    }elseif($base_name === "json"){
        $env = file_get_contents('.env');
        $env = preg_replace('/DB_SOURCE=.*/', "DB_SOURCE=json", $env);
        file_put_contents('.env', $env);
        echo 'Подключена база json';
    }else{
        echo 'Такой базы данных не существует';
    }



}

if ($argc < 2) {
    echo 'Укажите команду. Показать список команд: php index.php help';
    exit();
}
switch ($argv[1]) {
    case 'database':
        change_db($argv[2]);
        break;
    case 'show':
        showUsers();
        break;
    case 'add':
        addUser($argv[2] ?? null, $argv[3] ?? null, $argv[4] ?? null);
        break;
    case 'delete':
        if (isset($argv[2])) {
            deleteUser($argv[2]);
            break;
        } else {
            echo "Укажите Id";
            exit();
        }
    case 'help':
        echo "Показать пользователей: docker compose exec app php index.php show \n";
        echo "Выбрать базу данных: docker compose exec app php index.php database [mysql или json] \n";
        echo "Добавить пользователя: docker compose exec app php index.php add [имя], [фамилия], [email] \n";
        echo 'Удалить пользователя: docker compose exec app php index.php delete [id]';
        break;
    default:
        echo 'Неизвестная команда, введите: docker compose exec app php index.php help';
}



