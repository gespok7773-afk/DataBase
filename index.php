<?php
function showUsers()
{
    $users = json_decode(file_get_contents('users.json'));
    echo "Текущий список пользователей: \n";
    foreach ($users as $user) {
        echo $user->id . '. Имя: ' . $user->name . ', Фамилия:' . $user->surname . ', E-mail:' . $user->email . "\n";
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
    $users = json_decode(file_get_contents('users.json'), true);
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


    file_put_contents('users.json', json_encode($users, JSON_UNESCAPED_UNICODE));
}

function deleteUser($id)
{
    $users = json_decode(file_get_contents('users.json'), true);
    $found = false;
    foreach ($users as $key => $user) {
        if ($user['id'] == $id) {
            unset($users[$key]);
            $found = true;
        }
    }
    if ($found) {
        file_put_contents('users.json', json_encode($users, JSON_UNESCAPED_UNICODE));
        echo "Пользователь с ID: " . $id . " удален\n";
    } else {
        echo 'Пользователя c таким Id не существует';
    }


}

if ($argc < 2) {
    echo 'Укажите команду. Показать список команд: php index.php help';
    exit();
}
switch ($argv[1]) {
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
        echo "Показать пользователей: php index.php show \n";
        echo "Добавить пользователя: php index.php add [имя], [фамилия], [email] \n";
        echo 'Удалить пользователя: php index.php delete [id]';
        break;
    default:
        echo 'Неизвестная команда, введите: php index.php help';
}



