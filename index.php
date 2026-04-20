<?php
function showUsers()
{
    $users = json_decode(file_get_contents('users.json'));
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
        $email = strtr(strtolower($surname), $translit) . '@mail.ru';;
    }
    $users = json_decode(file_get_contents('users.json'), true);

    $ids = array_column($users, 'id');
    $id = empty($ids) ? 1 : max($ids) + 1;

    $users[] = ['id' => $id, 'name' => $name, 'surname' => $surname, 'email' => $email];
    file_put_contents('users.json', json_encode($users, JSON_UNESCAPED_UNICODE));
}

function deleteUser($id)
{
    $users = json_decode(file_get_contents('users.json'), true);
    foreach ($users as $key=>$user) {
        if ($user['id'] == $id) {
            unset($users[$key]);
        }
    }
    file_put_contents('users.json', json_encode($users, JSON_UNESCAPED_UNICODE));
}


addUser();
showUsers();