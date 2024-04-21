<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Google;
use Illuminate\Support\Facades\Log;
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

        // Configurar el proveedor de autenticación de Google
        $provider = new Google([
            'clientId'     => '276581705346-vjimpko04q6qh7e1l0gcsb435prgh0ek.apps.googleusercontent.com',
            'clientSecret' => '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',
             'redirectUri'  => 'http://localhost:8000/api/validartoken',
             'accessType'   => 'offline',
            // 'redirectUri'  => 'http://localhost:3000/login',

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
            
            // verificar si existe el usuario 
            $existingUser=User::where('email',$user['email'])->first();
            
            Log::info('usuario'.json_encode($existingUser));

            if(!$existingUser){
                // crear persona 
                $persona=new Persona();
                $persona->nombre=$user['given_name'];
                $persona->apellido=$user['family_name'];
                $persona->save();

                // crear clase de usuarios 
                $usuario=new User();
                $usuario->name=$user['name'];
                $usuario->email=$user['email'];
                $usuario->persona()->associate($persona); 
                $usuario->save();
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

    
        // Configurar el proveedor de autenticación de Google
        $provider = new Google([
            'clientId'     => '276581705346-vjimpko04q6qh7e1l0gcsb435prgh0ek.apps.googleusercontent.com',
            'clientSecret' => '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',
            'redirectUri'  => 'http://localhost:8000/api/validartoken',
            'accessType'   => 'offline',
        ]);

        
        // Generar una URL de autorización de Google    
        $authUrl = $provider->getAuthorizationUrl([
            'scope' => ['email', 'profile'],
        ]);
        return $authUrl;
        // Redirigir al usuario a la página de autenticación de Google
        return redirect()->away($authUrl);
    }
}
