<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

class EnclosureOpenApi
{
    #[OA\Get(
        path: '/api/enclosures',
        summary: 'List all enclosures',
        tags: ['Enclosures'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Enclosure list',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Carnivore Habitat A'),
                            new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                            new OA\Property(property: 'positionX', type: 'integer', example: 10),
                            new OA\Property(property: 'positionY', type: 'integer', example: 20),
                            new OA\Property(property: 'size', type: 'integer', example: 100),
                        ],
                        type: 'object',
                    ),
                ),
            ),
        ],
    )]
    public function list(): void {}

    #[OA\Get(
        path: '/api/enclosures/{id}',
        summary: 'Get one enclosure',
        tags: ['Enclosures'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Enclosure found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Carnivore Habitat A'),
                        new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                        new OA\Property(property: 'positionX', type: 'integer', example: 10),
                        new OA\Property(property: 'positionY', type: 'integer', example: 20),
                        new OA\Property(property: 'size', type: 'integer', example: 100),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Enclosure not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Enclosure not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function getOne(): void {}

    #[OA\Post(
        path: '/api/enclosures',
        summary: 'Create an enclosure',
        tags: ['Enclosures'],
        parameters: [
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
                required: ['name', 'clearance', 'positionX', 'positionY', 'size'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Herbivore Habitat B'),
                    new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                    new OA\Property(property: 'positionX', type: 'integer', example: 15),
                    new OA\Property(property: 'positionY', type: 'integer', example: 25),
                    new OA\Property(property: 'size', type: 'integer', example: 120),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: 'Enclosure created'),
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
        ],
    )]
    public function create(): void {}

    #[OA\Put(
        path: '/api/enclosures/{id}',
        summary: 'Update an enclosure',
        tags: ['Enclosures'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'clearance', 'positionX', 'positionY', 'size'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Herbivore Habitat B'),
                    new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                    new OA\Property(property: 'positionX', type: 'integer', example: 15),
                    new OA\Property(property: 'positionY', type: 'integer', example: 25),
                    new OA\Property(property: 'size', type: 'integer', example: 120),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: 'Enclosure updated'),
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
                description: 'Enclosure not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Enclosure not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/api/enclosures/{id}',
        summary: 'Delete an enclosure',
        tags: ['Enclosures'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Enclosure deleted'),
            new OA\Response(
                response: 404,
                description: 'Enclosure not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Enclosure not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function delete(): void {}
}
