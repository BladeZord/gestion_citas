<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Intentar parsear JSON del contenido, independientemente del Content-Type
        $content = $request->getContent();
        
        if (!empty($content)) {
            // Intentar decodificar como JSON
            $json = json_decode($content, true);
            
            // Si es JSON válido, agregarlo al request
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                $request->merge($json);
            }
            // Si el Content-Type es application/json pero falló el parseo, loguear
            elseif (str_contains($request->header('Content-Type', ''), 'application/json')) {
                \Log::warning('ForceJsonResponse: JSON inválido recibido', [
                    'content' => substr($content, 0, 200),
                    'json_error' => json_last_error_msg()
                ]);
            }
        }

        // Forzar respuesta JSON
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
