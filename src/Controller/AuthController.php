<?php

namespace Src\Controller;

use Core\JWT;
use Core\Request;
use Core\Response;
use Src\Model\User;

class AuthController
{

    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register(Request $request, Response $response)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // Validate input
        if (!$email || !$password) {
            return $response->error('Email and password are required', 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $response->error('Invalid email format', 400);
        }

        if (strlen($password) < 6) {
            return $response->error('Password must be at least 6 characters', 400);
        }

        // Check if email already exists
        $existingUser = $this->userModel->findByEmail($email);

        if ($existingUser) {
            return $response->error('Email already registered', 409);
        }

        // Create new user
        $userId = $this->userModel->create([
            'email' => $email,
            'password' => $password,
            'firstname' => $request->input('firstname', ''),
            'lastname' => $request->input('lastname', ''),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $response->json([
            'message' => 'User registered successfully',
            'user_id' => $userId
        ]);
    }

    public function login(Request $request, Response $response)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // Validate input
        if (!$email || !$password) {
            return $response->error('Email and password are required', 400);
        }

        // Find user by email
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return $response->error('Invalid credentials', 401);
        }

        // Verify password
        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            return $response->error('Invalid credentials', 401);
        }

        // Generate JWT token
        $token = JWT::generate([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'iat' => time()
        ]);

        return $response->json([
            'message' => 'Login successful',
            'token' => $token
        ]);
    }
}
