<?php
class MysqlUserRepository implements UserRepositoryInterface
{
    private $pdo;

    public function __construct(array $chosen_db)
    {
        $this->pdo = new PDO("mysql:host={$chosen_db['DB_HOST']};dbname={$chosen_db['DB_NAME']};charset=utf8", $chosen_db['DB_USER'], $chosen_db['DB_PASSWORD']);
    }

    public function getData(): array
    {
        return $this->pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add(string $name, string $surname, string $email): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, surname, email) VALUES (?, ?, ?)");
        $stmt->execute([$name, $surname, $email]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() >= 1;
    }
}