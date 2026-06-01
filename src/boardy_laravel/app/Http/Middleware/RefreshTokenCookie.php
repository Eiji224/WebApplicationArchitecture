<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->isSuccessful() && $request->is('oauth/token')) {
            $data = json_decode($response->getContent(), true);

            if (is_array($data) && isset($data['refresh_token'])) {

                $refresh = $data['refresh_token'];

                unset($data['refresh_token']);
                $response->setContent(json_encode($data));

                $response->headers->setCookie(cookie(
                    'refresh_token',  // Имя куки
                    $refresh,          // Значение
                    60 * 24 * 30,      // Время жизни (30 дней в минутах)
                    '/',               // Путь (доступна на всем домене)
                    null,              // Домен (null — текущий)
                    true,              // Secure (передача только по HTTPS)
                    true,              // HttpOnly (строго недоступно для JavaScript!)
                    false,             // Raw
                    'Strict'           // SameSite защищает от CSRF
                ));
            }
        }

        return $response;
    }
}
