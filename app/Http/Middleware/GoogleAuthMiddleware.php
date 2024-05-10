<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\PasswordResetToken;


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
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['error' => 'Token de acceso no proporcionado'], 401);
            }

            $user = $this->authenticateWithGoogle($token);

            Auth::setUser($user);

            return $next($request);
        } catch (Exception $e) {
            Log::error('Error en el middleware GoogleAuthMiddleware: ' . $e->getMessage());
            return response()->json(['error' => 'Ha ocurrido un error'], 500);
        }
    }

    private function authenticateWithGoogle($token)
    {
        DB::enableQueryLog();
        Log::info('paso por authenticated with google');
        $client = new Client();

        try {

            $response = $client->get('https://www.googleapis.com/oauth2/v3/tokeninfo', [
                'query' => ['access_token' => $token]
            ]);
            $body = json_decode($response->getBody());
            if ($response->getStatusCode() === 200 && isset($body->email)) {
                return User::firstOrCreate(['email' => $body->email]);
            }
        } catch (\Exception $ex) {

            Log::error('Error  en el token');
            Log::error($token);
            // Usamos trim para quitar las comillas al principio y al final del token
            $token = trim($token, '"');
            // Si el token no es válido, intentar actualizarlo si hay un refresh token
            $refreshToken = PasswordResetToken::findByTokenAndEmail($token);
            Log::info(json_encode($refreshToken));
                $queries = DB::getQueryLog();
                Log::info(json_encode($queries));
            if ($refreshToken) {
                Log::info('refresh_token '.json_encode($refreshToken->refresh_token));
                return $this->refreshTokenAndGetUser($refreshToken);
            } else {
                throw new Exception('Token de acceso inválido');
            }
        }
    }

    private function refreshTokenAndGetUser($refreshToken)
    {
        $client = new Client();

        $response = $client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'client_id' => env('CLIENT_ID'),
                'client_secret' => env('CLIENT_SECRET'),
                'refresh_token' => $refreshToken->refresh_token,
                'grant_type' => 'refresh_token',
            ]
        ]);

        $body = json_decode($response->getBody());
        if ($response->getStatusCode() === 200 && isset($body->access_token)) {

            try{

                $response=$client->post('https://oauth2.googleapis.com/token', [
                    'form_params' => [
                        'client_id' => env('CLIENT_ID'),
                        'client_secret' => env('CLIENT_SECRET'),
                        'redirect_uri' => env('REDIRECT_URI'),
                        'grant_type' => 'authorization_code',
                        'code'=> $body->access_token
                    ]
                ]);

                $body=json_decode($response->getBody());

            }catch(\Exception $ex){
                Log::error(json_encode($ex->getMessage()));
            }


            // Llamar recursivamente a authenticateWithGoogle con el nuevo access token
            return $this->authenticateWithGoogle($body->access_token);
        } else {
            throw new Exception('Refresh token vencido');
        }
    }

    
}
