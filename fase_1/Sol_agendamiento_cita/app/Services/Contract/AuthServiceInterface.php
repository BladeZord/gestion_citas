<?php

namespace App\Services\Contract;

interface AuthServiceInterface
{
    public function login(string $email, string $password): array;
    public function updatePassword(int $userId, string $passwordActual, string $passwordNueva): bool;
    public function hashPassword(string $password): string;
}
