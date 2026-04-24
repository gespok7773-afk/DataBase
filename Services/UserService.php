<?php
class UserService
{
    public function __construct(private UserRepositoryInterface $repository)
    {
    }

    public function showUsers()
    {
        $users = $this->repository->getData();
        echo "Текущий список пользователей: \n";
        foreach ($users as $user) {
            echo $user['id'] . '. Имя: ' . $user['name'] . ', Фамилия:' . $user['surname'] . ', E-mail:' . $user['email'] . "\n";
        }
    }

    public function add($name = null, $surname = null, $email = null)
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
        foreach ($this->repository->getData() as $user) {
            if ($user['email'] === $email) {
                echo "Такой пользователь уже существует \n";
                return;
            }
        }
        $this->repository->add($name, $surname, $email);
        echo 'Пользователь ' . $name . ' ' . $surname . " добавлен \n";

    }

    public function delete(int $id)
    {
        if ($this->repository->delete($id)) {
            echo "Пользователь с ID $id удалён\n";
        } else {
            echo "Пользователя с таким ID не существует\n";
        }
    }

    function changeDb($baseName)
    {
        if ($baseName !== 'mysql' && $baseName !== 'json') {
            echo 'Такой базы данных не существует \n';
            return;
        }
        $env = file_get_contents('.env');
        $env = preg_replace('/DB_SOURCE=.*/', "DB_SOURCE=$baseName", $env);
        file_put_contents('.env', $env);
        echo "Подключена база $baseName \n";
    }
}