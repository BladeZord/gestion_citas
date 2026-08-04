<?php

namespace App\Repository\Impl;

use App\Models\Usuario;
use App\Repository\Contract\UsuarioRepositoryInterface;
use Illuminate\Support\Facades\Log;

class UsuarioRepository implements UsuarioRepositoryInterface
{
    public function findAll(): \Illuminate\Database\Eloquent\Collection
    {
        Log::info('Repository: Buscando todos los usuarios');
        
        $usuarios = Usuario::all();
        
        Log::info('Repository: Usuarios obtenidos', ['total' => $usuarios->count()]);
        
        return $usuarios;
    }

    public function findByEmail(string $email): ?Usuario
    {
        Log::info('Repository: Buscando usuario por email', ['email' => $email]);
        
        $usuario = Usuario::where('correo', $email)->first();
        
        if ($usuario) {
            Log::info('Repository: Usuario encontrado', ['usuario_id' => $usuario->id]);
        } else {
            Log::warning('Repository: Usuario no encontrado', ['email' => $email]);
        }
        
        return $usuario;
    }

    public function findById(int $id): ?Usuario
    {
        Log::info('Repository: Buscando usuario por ID', ['usuario_id' => $id]);
        
        $usuario = Usuario::find($id);
        
        if ($usuario) {
            Log::info('Repository: Usuario encontrado', ['usuario_id' => $id]);
        } else {
            Log::warning('Repository: Usuario no encontrado', ['usuario_id' => $id]);
        }
        
        return $usuario;
    }

    public function updateToken(int $id, string $token): bool
    {
        Log::info('Repository: Actualizando token de usuario', ['usuario_id' => $id]);
        
        $result = Usuario::where('id', $id)->update(['remember_token' => $token]);
        
        if ($result) {
            Log::info('Repository: Token actualizado exitosamente', ['usuario_id' => $id]);
        } else {
            Log::error('Repository: Error al actualizar token', ['usuario_id' => $id]);
        }
        
        return $result > 0;
    }

    public function updatePassword(int $id, string $password): bool
    {
        Log::info('Repository: Actualizando contraseña de usuario', ['usuario_id' => $id]);
        
        $usuario = Usuario::find($id);
        if (!$usuario) {
            Log::warning('Repository: Usuario no encontrado para actualizar contraseña', ['usuario_id' => $id]);
            return false;
        }
        
        // El mutator setPasswordAttribute() se encargará de hashear la contraseña
        $usuario->password = $password;
        $result = $usuario->save();
        
        if ($result) {
            Log::info('Repository: Contraseña actualizada exitosamente', ['usuario_id' => $id]);
        } else {
            Log::error('Repository: Error al actualizar contraseña', ['usuario_id' => $id]);
        }
        
        return $result;
    }

    public function create(array $data): Usuario
    {
        Log::info('Repository: Creando nuevo usuario', ['data' => array_merge($data, ['password' => '***'])]);
        
        $usuario = Usuario::create($data);
        
        Log::info('Repository: Usuario creado exitosamente', ['usuario_id' => $usuario->id]);
        
        return $usuario;
    }

    public function update(int $id, array $data): bool
    {
        Log::info('Repository: Actualizando usuario', [
            'usuario_id' => $id,
            'data' => isset($data['password']) ? array_merge($data, ['password' => '***']) : $data
        ]);
        
        $usuario = Usuario::find($id);
        if (!$usuario) {
            Log::warning('Repository: Usuario no encontrado para actualizar', ['usuario_id' => $id]);
            return false;
        }
        
        $result = $usuario->update($data);
        
        if ($result) {
            Log::info('Repository: Usuario actualizado exitosamente', ['usuario_id' => $id]);
        } else {
            Log::error('Repository: Error al actualizar usuario', ['usuario_id' => $id]);
        }
        
        return $result;
    }

    public function delete(int $id): bool
    {
        Log::info('Repository: Eliminando usuario', ['usuario_id' => $id]);
        
        $usuario = Usuario::find($id);
        if (!$usuario) {
            Log::warning('Repository: Usuario no encontrado para eliminar', ['usuario_id' => $id]);
            return false;
        }
        
        $result = $usuario->delete();
        
        if ($result) {
            Log::info('Repository: Usuario eliminado exitosamente', ['usuario_id' => $id]);
        } else {
            Log::error('Repository: Error al eliminar usuario', ['usuario_id' => $id]);
        }
        
        return $result;
    }
}
