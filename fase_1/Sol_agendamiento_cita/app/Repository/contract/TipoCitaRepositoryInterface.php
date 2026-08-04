<?php

namespace App\Repository\Contract;

use App\Models\TipoCita;
use Illuminate\Database\Eloquent\Collection;

interface TipoCitaRepositoryInterface
{
    public function findAll(): Collection;
    public function findById(int $id): ?TipoCita;
    public function create(array $data): TipoCita;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function hasCitas(int $id): bool;
}
