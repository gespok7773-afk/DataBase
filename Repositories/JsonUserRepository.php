<?php
class JsonUserRepository implements UserRepositoryInterface
{
    public function getData(): array
    {
        return json_decode(file_get_contents('users.json'), true);
    }

    public function add(string $name, string $surname, string $email): void
    {
        $users = $this->getData();
        $ids = array_column($users, 'id');
        $id = empty($ids) ? 1 : max($ids) + 1;
        $users[] = ['id' => $id, 'name' => $name, 'surname' => $surname, 'email' => $email];
        file_put_contents('users.json', json_encode($users, JSON_UNESCAPED_UNICODE));
    }

    public function delete(int $id): bool
    {
        $users = $this->getData();
        $found = false;
        foreach ($users as $key => $user) {
            if ($user['id'] == $id) {
                unset($users[$key]);
                $found = true;
                break;
            }
        }
        if ($found) {
            file_put_contents('users.json', json_encode($users, JSON_UNESCAPED_UNICODE));
        }
        return $found;
    }

}