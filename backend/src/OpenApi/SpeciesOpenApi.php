<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

class SpeciesOpenApi
{
    #[OA\Get(
        path: '/api/species',
        summary: 'List all species',
        tags: ['Species'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Species list',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Tyrannosaurus rex'),
                            new OA\Property(property: 'diet', type: 'string', enum: ['carnivorous', 'herbivorous', 'omnivorous']),
                            new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                        ],
                        type: 'object',
                    ),
                ),
            ),
        ],
    )]
    public function list(): void {}

    #[OA\Get(
        path: '/api/species/{id}',
        summary: 'Get one species',
        tags: ['Species'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Species found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Tyrannosaurus rex'),
                        new OA\Property(property: 'diet', type: 'string', enum: ['carnivorous', 'herbivorous', 'omnivorous']),
                        new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Species not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Species not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function getOne(): void {}

    #[OA\Post(
        path: '/api/species',
        summary: 'Create a species',
        tags: ['Species'],
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
                required: ['name', 'diet', 'clearance'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Allosaurus'),
                    new OA\Property(property: 'diet', type: 'string', enum: ['carnivorous', 'herbivorous', 'omnivorous']),
                    new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: 'Species created'),
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
        path: '/api/species/{id}',
        summary: 'Update a species',
        tags: ['Species'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'diet', 'clearance'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Allosaurus'),
                    new OA\Property(property: 'diet', type: 'string', enum: ['carnivorous', 'herbivorous', 'omnivorous']),
                    new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: 'Species updated'),
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
                description: 'Species not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Species not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/api/species/{id}',
        summary: 'Delete a species',
        tags: ['Species'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Species deleted'),
            new OA\Response(
                response: 404,
                description: 'Species not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Species not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function delete(): void {}
}
