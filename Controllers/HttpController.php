<?php

class HttpController
{
    public function __construct(private UserService $userService)
    {
    }
    public function request()
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $path === '/list-users') {
            echo json_encode($this->userService->getUsers(), JSON_UNESCAPED_UNICODE);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && preg_match('/\/delete-user\/(\d+)/', $path, $matches)) {
            $id = $matches[1];
            $this->userService->delete($id);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/create-user') {
            $users = json_decode(file_get_contents('php://input'), true);
            if (array_is_list($users)) {
                foreach ($users as $user) {
                    $this->userService->add($user['name'] ?? null, $user['surname'] ?? null, $user['email'] ?? null);
                }
            } else {
                $this->userService->add($users['name'] ?? null, $users['surname'] ?? null, $users['email'] ?? null);
            }
        }
    }
}




