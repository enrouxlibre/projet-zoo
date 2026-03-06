<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

class AuthOpenApi
{
    #[OA\Post(
        path: '/api/login',
        summary: 'Authenticate user',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'your-password'),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Login successful.'),
                        new OA\Property(property: 'csrfToken', type: 'string', example: 'a6c5...'),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(response: 401, description: 'Invalid credentials'),
        ],
    )]
    public function login(): void {}

    #[OA\Post(
        path: '/api/logout',
        summary: 'Logout current user',
        tags: ['Authentication'],
        parameters: [
            new OA\Parameter(
                name: 'X-CSRF-TOKEN',
                in: 'header',
                required: true,
                description: 'CSRF token for authentication',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function logout(): void {}
}
