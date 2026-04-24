<?php
interface UserRepositoryInterface
{
    public function getData(): array;

    public function add(string $name, string $surname, string $email): void;

    public function delete(int $id): bool;
}