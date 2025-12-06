<?php

namespace App\Repository\Contract;

use App\Models\Usuario;

interface UsuarioRepositoryInterface
{
    public function findAll(): \Illuminate\Database\Eloquent\Collection;
    public function findByEmail(string $email): ?Usuario;
    public function findById(int $id): ?Usuario;
    public function create(array $data): Usuario;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function updateToken(int $id, string $token): bool;
    public function updatePassword(int $id, string $password): bool;
}
