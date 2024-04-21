<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Obtener el token
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token de acceso no proporcionado'], 401);
        }

        // Validar el token de acceso con Google
        $client = new Client();
        try {
            $response = $client->get('https://www.googleapis.com/oauth2/v3/tokeninfo', [
                'query' => [
                    'access_token' => $token
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Token de acceso inválido'], 401);
        }
        $body = json_decode($response->getBody());

        // Verificar si la solicitud fue exitosa y si el token es válido
        if ($response->getStatusCode() === 200 && isset($body->email)) {
                
            // Buscar o crear el usuario en la base de datos
            $user = User::firstOrCreate(['email' => $body->email]);
            // Establecer el usuario autenticado
            Auth::setUser($user);

            // Permitir que la solicitud continúe
            return $next($request);
        } else {
            // Si el token no es válido, responder con un error de "Unauthorized"
            return response()->json(['error' => 'Token de acceso inválido'], 401);
        }
    }
}
