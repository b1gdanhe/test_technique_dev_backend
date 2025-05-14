<?php

namespace Src\Middleware;

use Core\JWT;
use Core\Request;
use Core\Response;
use Models\User;

class AuthMiddleware
{
    public function handle(Request $request, Response $response): bool
    {
        $token = $request->getAuthToken();
        if (!$token) {
            $response->error('Unauthorized - No token provided', 401);
            return false;
        }

        $payload = JWT::validate($token);

        if (!$payload || !isset($payload['user_id'])) {
            $response->error('Unauthorized - Invalid token', 401);
            return false;
        }

        // Add user ID to request for controllers to use
        $request->setAuthId($payload['user_id']);

        return true;
    }
}
