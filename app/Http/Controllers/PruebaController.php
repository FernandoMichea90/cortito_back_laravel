<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Google;
use Illuminate\Support\Facades\Log;
use App\Models\PasswordResetToken;
use GuzzleHttp\Client;
use Exception;


class PruebaController extends Controller
{
    /**
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function validar(Request $request)
    {
        // Obtener el código de autorización de la URL
        $code = $request->input('code');
        // Verificar si se proporcionó el código de autorización
        if (!$code) {
            return response()->json(['error' => 'Código de autorización no proporcionado'], 400);
        }

        $provider = new Google([
            'clientId'     => env('CLIENT_ID'),
            'clientSecret' => env('CLIENT_SECRET'),
            'redirectUri'  => env('REDIRECT_URI'),
            'grant_type'   => 'authorization_code',
        ]);

        try {
            // Intercambiar el código de autorización por un token de acceso
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            // Obtener el token de acceso
            $token = $accessToken->getToken();
            // Obtener el refreshToken
            $refreshToken = $accessToken->getRefreshToken();

            // Usar el AccessToken para obtener los datos del usuario
            $resourceOwner = $provider->getResourceOwner($accessToken);
            $user = $resourceOwner->toArray();

            Log::info("datos del usuario");
            Log::info(json_encode($user));
            // verificar si existe el usuario 
            $existingUser = User::where('email', $user['email'])->first();

            Log::info('usuario' . json_encode($existingUser));

            if (!$existingUser) {
                // crear persona 
                $persona = new Persona();
                $persona->nombre = $user['given_name'];
                $persona->apellido = $user['family_name'];
                $persona->save();

                // crear clase de usuarios 
                $usuario = new User();
                $usuario->name = $user['name'];
                $usuario->email = $user['email'];
                $usuario->persona()->associate($persona);
                $usuario->save();
            }

            try {
                $object_token = new PasswordResetToken();
                $object_token->email = $user['email'];
                $object_token->token = $token;
                $object_token->refresh_token = $refreshToken;
                $object_token->save();
            } catch (\Exception $e) {
                Log::error('Error creating password reset token: ' . $e->getMessage());
            }
            // Devolver los datos del usuario y el refreshToken

            return redirect('http://localhost:3000/#access_token=' . $token);
        } catch (\Exception $e) {
            Log::info('Ocurrió un error', ['exception' => $e]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function redirectToGoogle(Request $request)
    {

        // create client 
        $client = new Client();
        // Obtener el valor del parámetro 'view' desde la solicitud
        $view = $request->input('view');

        // Verificar si el parámetro 'view' está vacío o no se ha proporcionado
        if ($view === null || $view === '') {
            // Si está vacío, establecer $view en false
            $view = true;
        }
        try {

            // Construir la consulta de URL
            $queryParams = [
                'scope' => 'openid email profile',
                'access_type' => 'offline',
                'include_granted_scopes' => 'true',
                'response_type' => 'code',
                'redirect_uri' => env('REDIRECT_URI'),
                'client_id' => env('CLIENT_ID'),
            ];

            $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($queryParams);

            // Redirigir al usuario a la URL de autorización de Google
            // return redirect($url);

        } catch (\Exception $ex) {
            response()->json(['error' => 'ha ocurrido un error', 'error_info' => $ex]);
        }

        if ($view) {
            // Redirigir al usuario a la página de autenticación de Google
            return redirect()->away($authUrl);
        } else {
            return $authUrl;
        }
    }


   

    /**
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function refreshToken(Request $request)
    {

        $client = new Client();
        $refresh_token = $request->input('refresh_token');
        if (!$refresh_token) {
            return response()->json(['error' => 'esto es un error'], 400);
        }
        try {

            $response = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'refresh_token' => $refresh_token,
                    'client_id' => env('CLIENT_ID'),
                    'client_secret' => env('CLIENT_SECRET'),
                    'grant_type' => 'refresh_token'
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return response()->json(['respuesta' => $data], 200);
        } catch (\Exception $ex) {

            return response()->json(['error' => 'ha ocurrido un error', 'error_msj' => $ex], 400);
        }
    }


    /**
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */

    public function refreshToken(Request $request)
    {

        $request->validate([
            'token' => 'required|string', 'email' => 'required|string'
        ]);
        $token = $request->token;
        $email = $request->email;

        $token = trim($token, '"');
        $client = new Client();
        try {
            $client->get('https://www.googleapis.com/oauth2/v3/tokeninfo', [
                'query' => ['access_token' => $token]
            ]);
            // si el token es valido no genera error, si es invalido genera un error -> catch
            return response()->json(['token' => $token, 'mensajes' => 'se recupera el token'], 200);
        } catch (\Exception $ex) {
            Log::info('llego hasta aca');

        
                Log::info($token);
                $refreshToken = PasswordResetToken::where('token',$token)->first();
                Log::info(json_encode($refreshToken));
                // if exists refresh token 

                if ($refreshToken->refresh_token) {
                    // get new token or access_token
                    try {
                        $response = $client->post('https://oauth2.googleapis.com/token', [
                            'form_params' => [
                                'client_id' => env('CLIENT_ID'),
                                'client_secret' => env('CLIENT_SECRET'),
                                'refresh_token' => $refreshToken->refresh_token,
                                'grant_type' => 'refresh_token',
                            ]
                        ]);
                    } catch (\Exception $ex) {
                        // Manejar la excepción generada por el cliente HTTP
                        Log::error('Error al realizar la solicitud HTTP: ' . $ex->getMessage());
                        return response()->json(['error' => 'Error en la solicitud HTTP','token'=>NULL], 500);
                    }
                    Log::info('paso despues del error');
                    $body = json_decode($response->getBody());
                    Log::info('llego hasta aca');
                    if ($response->getStatusCode() === 200 && isset($body->access_token)) {
                        // update table password reset tokens
                        PasswordResetToken::where('id', $refreshToken->id)->update(['token' => $body->access_token]);
                        return response()->json(['token' => $body->access_token, 'mensaje' => 'token actualizado'], 200);
                    }
                }
          
        }
    }
}
