<?php

namespace App\Services\Contract;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Collection;

interface UsuarioServiceInterface
{
    public function listarUsuarios(): Collection;
    public function obtenerUsuario(int $id): ?Usuario;
    public function crearUsuario(array $data): Usuario;
    public function actualizarUsuario(int $id, array $data): Usuario;
    public function eliminarUsuario(int $id): bool;
}
