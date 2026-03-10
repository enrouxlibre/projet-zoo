<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

class AnimalsOpenApi
{
    #[OA\Get(
        path: '/api/animals',
        summary: 'List all animals',
        tags: ['Animals'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Animals list',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                            new OA\Property(property: 'name', type: 'string', example: 'Blue'),
                            new OA\Property(property: 'gender', type: 'boolean', example: true),
                            new OA\Property(property: 'weight', type: 'integer', example: 2200),
                            new OA\Property(property: 'size', type: 'integer', example: 12),
                            new OA\Property(property: 'age', type: 'integer', example: 7),
                            new OA\Property(property: 'speciesId', type: 'integer', example: 1),
                            new OA\Property(property: 'enclosureId', type: 'integer', example: 1),
                            new OA\Property(
                                property: 'species',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Triceratops'),
                                    new OA\Property(property: 'diet', type: 'string', example: 'herbivorous'),
                                    new OA\Property(property: 'clearance', type: 'integer', example: 2),
                                ],
                                type: 'object',
                            ),
                            new OA\Property(
                                property: 'enclosure',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Plains Zone'),
                                    new OA\Property(property: 'clearance', type: 'integer', example: 2),
                                    new OA\Property(property: 'positionX', type: 'integer', example: 15),
                                    new OA\Property(property: 'positionY', type: 'integer', example: 8),
                                    new OA\Property(property: 'size', type: 'integer', example: 120),
                                ],
                                type: 'object',
                            ),
                        ],
                        type: 'object',
                    ),
                ),
            ),
        ],
    )]
    public function list(): void {}

    #[OA\Get(
        path: '/api/animals/{id}',
        summary: 'Get one animal',
        tags: ['Animals'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Animal found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                        new OA\Property(property: 'name', type: 'string', example: 'Blue'),
                        new OA\Property(property: 'gender', type: 'boolean', example: true),
                        new OA\Property(property: 'weight', type: 'integer', example: 2200),
                        new OA\Property(property: 'size', type: 'integer', example: 12),
                        new OA\Property(property: 'age', type: 'integer', example: 7),
                        new OA\Property(property: 'speciesId', type: 'integer', example: 1),
                        new OA\Property(property: 'enclosureId', type: 'integer', example: 1),
                        new OA\Property(
                            property: 'species',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Triceratops'),
                                new OA\Property(property: 'diet', type: 'string', example: 'herbivorous'),
                                new OA\Property(property: 'clearance', type: 'integer', example: 2),
                            ],
                            type: 'object',
                        ),
                        new OA\Property(
                            property: 'enclosure',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Plains Zone'),
                                new OA\Property(property: 'clearance', type: 'integer', example: 2),
                                new OA\Property(property: 'positionX', type: 'integer', example: 15),
                                new OA\Property(property: 'positionY', type: 'integer', example: 8),
                                new OA\Property(property: 'size', type: 'integer', example: 120),
                            ],
                            type: 'object',
                        ),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Animal not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Animal not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function getOne(): void {}

    #[OA\Post(
        path: '/api/animals',
        summary: 'Create an animal',
        tags: ['Animals'],
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
                required: ['name', 'gender', 'weight', 'size', 'age', 'speciesId', 'enclosureId'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Blue'),
                    new OA\Property(property: 'gender', type: 'boolean', example: true),
                    new OA\Property(property: 'weight', type: 'integer', example: 2200),
                    new OA\Property(property: 'size', type: 'integer', example: 12),
                    new OA\Property(property: 'age', type: 'integer', example: 7),
                    new OA\Property(property: 'speciesId', type: 'integer', example: 1),
                    new OA\Property(property: 'enclosureId', type: 'integer', example: 1),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: 'Animal created'),
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
                description: 'Species or enclosure not found',
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
        path: '/api/animals/{id}',
        summary: 'Update an animal',
        tags: ['Animals'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'gender', 'weight', 'size', 'age', 'speciesId', 'enclosureId'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Blue'),
                    new OA\Property(property: 'gender', type: 'boolean', example: true),
                    new OA\Property(property: 'weight', type: 'integer', example: 2200),
                    new OA\Property(property: 'size', type: 'integer', example: 12),
                    new OA\Property(property: 'age', type: 'integer', example: 7),
                    new OA\Property(property: 'speciesId', type: 'integer', example: 1),
                    new OA\Property(property: 'enclosureId', type: 'integer', example: 1),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: 'Animal updated'),
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
                description: 'Animal, species or enclosure not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/api/animals/{id}',
        summary: 'Delete an animal',
        tags: ['Animals'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Animal deleted'),
            new OA\Response(
                response: 404,
                description: 'Animal not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Animal not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function delete(): void {}
}
