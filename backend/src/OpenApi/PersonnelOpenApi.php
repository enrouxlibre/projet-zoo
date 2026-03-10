<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

class PersonnelOpenApi
{
    #[OA\Get(
        path: '/api/personnel',
        summary: 'List personnel with profile and personnel information',
        tags: ['Personnel'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Personnel list',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'userId', type: 'integer', example: 1),
                            new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                            new OA\Property(property: 'firstName', type: 'string', example: 'John'),
                            new OA\Property(property: 'lastName', type: 'string', example: 'Doe'),
                            new OA\Property(property: 'telephone', type: 'string', nullable: true, example: '+33123456789'),
                            new OA\Property(property: 'job', type: 'string', example: 'Veterinarian'),
                            new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                            new OA\Property(property: 'dateOfBirth', type: 'string', format: 'date', example: '1990-05-21'),
                        ],
                        type: 'object',
                    ),
                ),
            ),
        ],
    )]
    public function list(): void {}

    #[OA\Get(
        path: '/api/personnel/{id}',
        summary: 'Get one personnel entry with profile and personnel information',
        tags: ['Personnel'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Personnel found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'userId', type: 'integer', example: 1),
                        new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                        new OA\Property(property: 'firstName', type: 'string', example: 'John'),
                        new OA\Property(property: 'lastName', type: 'string', example: 'Doe'),
                        new OA\Property(property: 'telephone', type: 'string', nullable: true, example: '+33123456789'),
                        new OA\Property(property: 'job', type: 'string', example: 'Veterinarian'),
                        new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                        new OA\Property(property: 'dateOfBirth', type: 'string', format: 'date', example: '1990-05-21'),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Personnel entry not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Personnel entry not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function getOne(): void {}

    #[OA\Put(
        path: '/api/personnel/{id}',
        summary: 'Update profile and personnel information',
        tags: ['Personnel'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(
                name: 'X-CSRF-TOKEN',
                in: 'header',
                required: true,
                description: 'CSRF token for authentication',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['firstName', 'lastName', 'telephone', 'job', 'clearance', 'dateOfBirth'],
                properties: [
                    new OA\Property(property: 'firstName', type: 'string', example: 'John'),
                    new OA\Property(property: 'lastName', type: 'string', example: 'Doe'),
                    new OA\Property(property: 'telephone', type: 'string', nullable: true, example: '+33123456789'),
                    new OA\Property(property: 'job', type: 'string', example: 'Veterinarian'),
                    new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                    new OA\Property(property: 'dateOfBirth', type: 'string', format: 'date', example: '1990-05-21'),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: 'Personnel updated'),
            new OA\Response(
                response: 400,
                description: 'Invalid JSON body or missing required fields',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string'),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Personnel entry not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Personnel entry not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function update(): void {}
}
