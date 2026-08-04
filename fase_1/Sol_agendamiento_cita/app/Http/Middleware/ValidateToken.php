<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\ApiResponse;
use App\Models\Usuario;

class ValidateToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return ApiResponse::error('Token no proporcionado', 401);
        }

        // Buscar usuario por token (asumiendo que el token se almacena en la tabla usuarios)
        // Para este ejemplo, usaremos un token estático o dinámico almacenado en la sesión/tabla
        $usuario = Usuario::where('remember_token', $token)->first();

        if (!$usuario) {
            return ApiResponse::error('Token inválido', 401);
        }

        // Agregar usuario al request para uso en controladores
        $request->merge(['usuario_autenticado' => $usuario]);

        return $next($request);
    }
}

