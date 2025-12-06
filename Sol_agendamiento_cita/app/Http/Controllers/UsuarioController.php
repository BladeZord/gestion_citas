<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Services\Contract\UsuarioServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UsuarioController extends Controller
{
    protected UsuarioServiceInterface $usuarioService;

    public function __construct(UsuarioServiceInterface $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    /**
     * Listar todos los usuarios
     */
    public function index()
    {
        Log::info('Controller: Listado de usuarios solicitado');

        try {
            $usuarios = $this->usuarioService->listarUsuarios();

            Log::info('Controller: Usuarios obtenidos exitosamente', ['total' => $usuarios->count()]);

            return ApiResponse::success($usuarios, 'Usuarios obtenidos exitosamente');
        } catch (\Exception $e) {
            Log::error('Controller: Error al listar usuarios', ['error' => $e->getMessage()]);
            return ApiResponse::error('Error al obtener los usuarios', 500);
        }
    }

    /**
     * Crear un nuevo usuario
     */
    public function store(StoreUsuarioRequest $request)
    {
        Log::info('Controller: Creación de usuario iniciada', [
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'rol' => $request->rol
        ]);

        try {
            $usuario = $this->usuarioService->crearUsuario($request->validated());

            Log::info('Controller: Usuario creado exitosamente', ['usuario_id' => $usuario->id]);

            return ApiResponse::success($usuario, 'Usuario creado exitosamente', 201);
        } catch (\Exception $e) {
            Log::error('Controller: Error al crear usuario', [
                'error' => $e->getMessage(),
                'data' => array_merge($request->validated(), ['password' => '***'])
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Obtener detalle de un usuario
     */
    public function show($id)
    {
        Log::info('Controller: Consulta de usuario solicitada', ['usuario_id' => $id]);

        try {
            $usuario = $this->usuarioService->obtenerUsuario($id);

            Log::info('Controller: Usuario obtenido exitosamente', ['usuario_id' => $id]);

            return ApiResponse::success($usuario, 'Usuario obtenido exitosamente');
        } catch (\Exception $e) {
            Log::error('Controller: Error al obtener usuario', [
                'usuario_id' => $id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Actualizar un usuario
     */
    public function update(UpdateUsuarioRequest $request, $id)
    {
        Log::info('Controller: Actualización de usuario iniciada', [
            'usuario_id' => $id,
            'campos' => array_keys($request->validated())
        ]);

        try {
            $usuario = $this->usuarioService->actualizarUsuario($id, $request->validated());

            Log::info('Controller: Usuario actualizado exitosamente', ['usuario_id' => $id]);

            return ApiResponse::success($usuario, 'Usuario actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Controller: Error al actualizar usuario', [
                'usuario_id' => $id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Eliminar un usuario
     */
    public function destroy($id)
    {
        Log::info('Controller: Eliminación de usuario solicitada', ['usuario_id' => $id]);

        try {
            $this->usuarioService->eliminarUsuario($id);

            Log::info('Controller: Usuario eliminado exitosamente', ['usuario_id' => $id]);

            return ApiResponse::success(null, 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Controller: Error al eliminar usuario', [
                'usuario_id' => $id,
                'error' => $e->getMessage()
            ]);

            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
