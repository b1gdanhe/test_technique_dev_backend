<?php

namespace Core;

class JWT
{
    private static $secret;
    private static $expiration_time;
    private static function init()
    {
        if (self::$secret === null) {
            $config_jwt = require base_path('config/jwt.php');
            self::$secret = $config_jwt['secret_key'];
            self::$expiration_time = $config_jwt['expiration_time'];
        }
    }
    public static function generate(array $payload): string
    {
        self::init();
        // Set expiration time (5 minutes from now)
        $payload['exp'] = time() + self::$expiration_time; // 5 minutes = 300 seconds

        // Create JWT parts
        $header = self::base64UrlEncode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]));

        $payload = self::base64UrlEncode(json_encode($payload));

        $signature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", self::$secret, true)
        );

        return "$header.$payload.$signature";
    }

    public static function validate(string $token): ?array
    {
        self::init();
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;

        // Verify signature
        $expectedSignature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", self::$secret, true)
        );

        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }

        // Decode payload
        $payload = json_decode(self::base64UrlDecode($payload), true);

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
