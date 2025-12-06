<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\HashPasswordRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Services\Contract\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Login de usuario
     */
    public function login(Request $request)
    {
        // Obtener datos del request (el middleware ya parseó el JSON si existe)
        $data = $request->all();
        
        // Debug: Ver qué está llegando en el request
        Log::info('Controller: Intento de login iniciado', [
            'data' => $data,
            'content_type' => $request->header('Content-Type'),
            'method' => $request->method(),
            'has_correo' => $request->has('correo'),
            'has_password' => $request->has('password'),
        ]);

        $validator = Validator::make($data, [
            'correo' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::warning('Controller: Error de validación en login', [
                'data' => $data,
                'errores' => $validator->errors()
            ]);
            return ApiResponse::error('Error de validación', 422, $validator->errors());
        }

        try {
            $result = $this->authService->login($data['correo'], $data['password']);

            Log::info('Controller: Login exitoso', ['correo' => $data['correo']]);

            return ApiResponse::success($result, 'Login exitoso', 200);
        } catch (\Exception $e) {
            Log::error('Controller: Error en login', [
                'correo' => $data['correo'] ?? null,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Actualizar contraseña de usuario
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $id = $request->id;
        
        Log::info('Controller: Actualización de contraseña solicitada', [
            'usuario_id' => $id,
            'has_password_actual' => $request->has('password_actual'),
            'has_password_nueva' => $request->has('password_nueva')
        ]);

        try {
            $result = $this->authService->updatePassword(
                (int)$id,
                $request->password_actual,
                $request->password_nueva
            );

            Log::info('Controller: Contraseña actualizada exitosamente', ['usuario_id' => $id]);

            return ApiResponse::success(null, 'Contraseña actualizada exitosamente', 200);
        } catch (\Exception $e) {
            Log::error('Controller: Error al actualizar contraseña', [
                'usuario_id' => $id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Hashear contraseña (recibe string plano y devuelve hash)
     */
    public function hashPassword(HashPasswordRequest $request)
    {
        Log::info('Controller: Hasheo de contraseña solicitado', [
            'password_length' => strlen($request->password),
            'has_password' => $request->has('password')
        ]);

        try {
            // 1. Recibir contraseña DESENCRIPTADA (texto plano)
            $passwordPlano = $request->password;
            
            // 2. ENCRIPTAR la contraseña
            $passwordHasheado = $this->authService->hashPassword($passwordPlano);

            Log::info('Controller: Contraseña hasheada exitosamente');

            return ApiResponse::success([
                'password_plano' => substr($passwordPlano, 0, 3) . '***', // Solo preview por seguridad
                'password_hasheado' => $passwordHasheado,
                'algoritmo' => 'bcrypt',
                'hash_length' => strlen($passwordHasheado)
            ], 'Contraseña hasheada exitosamente', 200);
        } catch (\Exception $e) {
            Log::error('Controller: Error al hashear contraseña', [
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}

