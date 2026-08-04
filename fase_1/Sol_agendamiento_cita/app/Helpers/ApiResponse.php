<?php

namespace App\Helpers;

class ApiResponse
{
    /**
     * Respuesta exitosa
     * Si no hay mensaje, devuelve solo los datos
     */
    public static function success($data = null, $message = null, $code = 200)
    {
        // Si no hay mensaje, devolver solo los datos (respuesta más simple)
        if (!$message) {
            return response()->json($data, $code);
        }

        // Si hay mensaje, devolver estructura con código, mensaje y datos
        $response = [
            'codigo' => $code,
            'mensaje' => $message,
        ];

        if ($data !== null) {
            $response['datos'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Respuesta de error
     */
    public static function error($message, $code = 400, $errors = null)
    {
        $response = [
            'codigo' => $code,
            'mensaje' => $message,
        ];

        if ($errors) {
            $response['errores'] = $errors;
        }

        return response()->json($response, $code);
    }
}

