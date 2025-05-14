<?php

namespace Src\Controller;

use Core\Request;
use Core\Response;
use Src\Model\User;

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function updateProfile(Request $request, Response $response)
    {
        $userId = $request->getAuthId(); // Set by AuthMiddleware
        $firstname = $request->input('firstname');
        $lastname = $request->input('lastname');

        // Validate input
        if ($firstname === null && $lastname === null) {
            return $response->error('No data provided for update', 400);
        }

        // Prepare update data
        $updateData = [];

        if ($firstname !== null) {
            $updateData['firstname'] = $firstname;
        }

        if ($lastname !== null) {
            $updateData['lastname'] = $lastname;
        }

        // Update user profile
        $success = $this->userModel->update($userId, $updateData);

        if (!$success) {
            return $response->error('Failed to update profile', 500);
        }

        return $response->json([
            'message' => 'Profile updated successfully'
        ]);
    }

    public function getUser(Request $request, Response $response)
    {
        $userId = $request->getAuthId(); // Set by AuthMiddleware
        // Get user data
        $user = $this->userModel->findById($userId);

        if (!$user) {
            return $response->error('User not found', 404);
        }

        // Remove password from response
        unset($user['password']);

        return $response->json([
            'user' => $user
        ]);
    }
}
