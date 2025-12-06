<?php

namespace App\Services\Impl;

use App\Models\Usuario;
use App\Repository\Contract\UsuarioRepositoryInterface;
use App\Services\Contract\UsuarioServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class UsuarioService implements UsuarioServiceInterface
{
    protected UsuarioRepositoryInterface $usuarioRepository;

    public function __construct(UsuarioRepositoryInterface $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function listarUsuarios(): Collection
    {
        Log::info('Service: Listando usuarios');

        $usuarios = $this->usuarioRepository->findAll();

        Log::info('Service: Usuarios listados exitosamente', ['total' => $usuarios->count()]);

        return $usuarios;
    }

    public function obtenerUsuario(int $id): ?Usuario
    {
        Log::info('Service: Obteniendo usuario', ['usuario_id' => $id]);

        $usuario = $this->usuarioRepository->findById($id);

        if (!$usuario) {
            Log::warning('Service: Usuario no encontrado', ['usuario_id' => $id]);
            throw new \Exception('Usuario no encontrado', 404);
        }

        Log::info('Service: Usuario obtenido exitosamente', ['usuario_id' => $id]);

        return $usuario;
    }

    public function crearUsuario(array $data): Usuario
    {
        Log::info('Service: Creando nuevo usuario', [
            'nombre' => $data['nombre'],
            'correo' => $data['correo'],
            'rol' => $data['rol']
        ]);

        // Verificar si el correo ya existe
        $usuarioExistente = $this->usuarioRepository->findByEmail($data['correo']);
        if ($usuarioExistente) {
            Log::warning('Service: Intento de crear usuario con correo duplicado', ['correo' => $data['correo']]);
            throw new \Exception('El correo ya está registrado', 422);
        }

        $usuario = $this->usuarioRepository->create($data);

        Log::info('Service: Usuario creado exitosamente', ['usuario_id' => $usuario->id]);

        return $usuario;
    }

    public function actualizarUsuario(int $id, array $data): Usuario
    {
        Log::info('Service: Actualizando usuario', ['usuario_id' => $id, 'campos' => array_keys($data)]);

        $usuario = $this->usuarioRepository->findById($id);
        if (!$usuario) {
            Log::warning('Service: Usuario no encontrado para actualizar', ['usuario_id' => $id]);
            throw new \Exception('Usuario no encontrado', 404);
        }

        // Si se actualiza el correo, verificar que no esté duplicado
        if (isset($data['correo']) && $data['correo'] !== $usuario->correo) {
            $usuarioExistente = $this->usuarioRepository->findByEmail($data['correo']);
            if ($usuarioExistente && $usuarioExistente->id !== $id) {
                Log::warning('Service: Intento de actualizar usuario con correo duplicado', [
                    'usuario_id' => $id,
                    'correo' => $data['correo']
                ]);
                throw new \Exception('El correo ya está registrado', 422);
            }
        }

        $this->usuarioRepository->update($id, $data);
        $usuario = $this->usuarioRepository->findById($id);

        Log::info('Service: Usuario actualizado exitosamente', ['usuario_id' => $id]);

        return $usuario;
    }

    public function eliminarUsuario(int $id): bool
    {
        Log::info('Service: Eliminando usuario', ['usuario_id' => $id]);

        $usuario = $this->usuarioRepository->findById($id);
        if (!$usuario) {
            Log::warning('Service: Usuario no encontrado para eliminar', ['usuario_id' => $id]);
            throw new \Exception('Usuario no encontrado', 404);
        }

        // Verificar si tiene citas asociadas
        $citasAsociadas = $usuario->citas()->count();
        if ($citasAsociadas > 0) {
            Log::warning('Service: No se puede eliminar usuario con citas asociadas', [
                'usuario_id' => $id,
                'citas_asociadas' => $citasAsociadas
            ]);
            throw new \Exception('No se puede eliminar el usuario porque tiene citas asociadas', 400);
        }

        $result = $this->usuarioRepository->delete($id);

        Log::info('Service: Usuario eliminado exitosamente', ['usuario_id' => $id]);

        return $result;
    }
}
