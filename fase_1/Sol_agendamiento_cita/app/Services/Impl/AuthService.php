<?php

namespace App\Services\Impl;

use App\Repository\Contract\UsuarioRepositoryInterface;
use App\Services\Contract\AuthServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthService implements AuthServiceInterface
{
    protected UsuarioRepositoryInterface $usuarioRepository;

    public function __construct(UsuarioRepositoryInterface $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function login(string $email, string $password): array
    {
        Log::info('Service: Iniciando proceso de login', ['email' => $email]);

        $usuario = $this->usuarioRepository->findByEmail($email);

        if (!$usuario) {
            Log::warning('Service: Usuario no encontrado para login', ['email' => $email]);
            throw new \Exception('Credenciales incorrectas', 401);
        }

        // 1. Usuario ingresa contraseña DESENCRIPTADA (texto plano)
        Log::info('Service: Contraseña recibida (texto plano)', [
            'email' => $email,
            'password_length' => strlen($password),
            'password_preview' => substr($password, 0, 3) . '***'
        ]);

        // 2. Obtener hash almacenado en la base de datos
        $passwordHashStored = $usuario->password;
        Log::info('Service: Hash almacenado en BD obtenido', [
            'email' => $email,
            'usuario_id' => $usuario->id,
            'hash_stored_prefix' => substr($passwordHashStored, 0, 20) . '...',
            'hash_algorithm' => substr($passwordHashStored, 0, 4) // $2y$ = bcrypt
        ]);

        // 3. ENCRIPTAR la contraseña ingresada y VALIDAR la igualdad
        // Hash::check() internamente:
        //   - Toma el hash almacenado (que contiene el salt y el algoritmo)
        //   - Hashea la contraseña ingresada con los mismos parámetros
        //   - Compara ambos hashes
        Log::info('Service: Encriptando contraseña ingresada y validando igualdad');
        $isValid = Hash::check($password, $passwordHashStored);
        
        Log::info('Service: Resultado de validación', [
            'email' => $email,
            'usuario_id' => $usuario->id,
            'password_ingresada_hasheada' => '*** (proceso interno de Hash::check)',
            'hash_almacenado' => substr($passwordHashStored, 0, 20) . '...',
            'validacion_exitosa' => $isValid,
            'proceso' => 'Hash::check() hashea la contraseña y compara con el hash almacenado'
        ]);

        if (!$isValid) {
            Log::warning('Service: Contraseña incorrecta - La contraseña hasheada NO coincide con el hash almacenado', [
                'email' => $email, 
                'usuario_id' => $usuario->id,
                'hash_stored_prefix' => substr($passwordHashStored, 0, 10),
                'razon' => 'El hash generado de la contraseña ingresada no coincide con el hash almacenado en BD'
            ]);
            throw new \Exception('Credenciales incorrectas', 401);
        }

        $token = Str::random(60);
        $this->usuarioRepository->updateToken($usuario->id, $token);

        Log::info('Service: Login exitoso', [
            'usuario_id' => $usuario->id,
            'email' => $usuario->correo,
            'rol' => $usuario->rol
        ]);

        return [
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'rol' => $usuario->rol,
            ]
        ];
    }

    public function updatePassword(int $userId, string $passwordActual, string $passwordNueva): bool
    {
        Log::info('Service: Iniciando actualización de contraseña', ['usuario_id' => $userId]);

        // 1. Buscar usuario por ID
        $usuario = $this->usuarioRepository->findById($userId);
        
        if (!$usuario) {
            Log::warning('Service: Usuario no encontrado para actualizar contraseña', ['usuario_id' => $userId]);
            throw new \Exception('Usuario no encontrado', 404);
        }

        // 2. Validar contraseña actual
        Log::info('Service: Validando contraseña actual', [
            'usuario_id' => $userId,
            'email' => $usuario->correo
        ]);

        $passwordHashStored = $usuario->password;
        $isPasswordActualValid = Hash::check($passwordActual, $passwordHashStored);

        Log::info('Service: Resultado de validación de contraseña actual', [
            'usuario_id' => $userId,
            'password_actual_valida' => $isPasswordActualValid,
            'hash_stored_prefix' => substr($passwordHashStored, 0, 20) . '...'
        ]);

        if (!$isPasswordActualValid) {
            Log::warning('Service: Contraseña actual incorrecta', [
                'usuario_id' => $userId,
                'email' => $usuario->correo
            ]);
            throw new \Exception('La contraseña actual es incorrecta', 401);
        }

        // 3. Validar que la nueva contraseña sea diferente a la actual
        if (Hash::check($passwordNueva, $passwordHashStored)) {
            Log::warning('Service: La nueva contraseña no puede ser igual a la actual', [
                'usuario_id' => $userId
            ]);
            throw new \Exception('La nueva contraseña debe ser diferente a la contraseña actual', 422);
        }

        // 4. Actualizar contraseña (el repository se encargará de hashearla)
        Log::info('Service: Actualizando contraseña', [
            'usuario_id' => $userId,
            'password_nueva_length' => strlen($passwordNueva)
        ]);

        $result = $this->usuarioRepository->updatePassword($userId, $passwordNueva);

        if (!$result) {
            Log::error('Service: Error al actualizar contraseña en repository', ['usuario_id' => $userId]);
            throw new \Exception('Error al actualizar la contraseña', 500);
        }

        Log::info('Service: Contraseña actualizada exitosamente', [
            'usuario_id' => $userId,
            'email' => $usuario->correo
        ]);

        return true;
    }

    public function hashPassword(string $password): string
    {
        Log::info('Service: Hasheando contraseña', [
            'password_length' => strlen($password),
            'password_preview' => substr($password, 0, 3) . '***'
        ]);

        // 1. Recibir contraseña DESENCRIPTADA (texto plano)
        // 2. ENCRIPTAR la contraseña usando bcrypt
        $hashedPassword = Hash::make($password);

        Log::info('Service: Contraseña hasheada exitosamente', [
            'hash_prefix' => substr($hashedPassword, 0, 20) . '...',
            'hash_algorithm' => substr($hashedPassword, 0, 4), // $2y$ = bcrypt
            'hash_length' => strlen($hashedPassword)
        ]);

        return $hashedPassword;
    }
}
