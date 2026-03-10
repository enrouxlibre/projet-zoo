<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Animals', description: 'Animal management endpoints')]
#[OA\Tag(name: 'Species', description: 'Species management endpoints')]
#[OA\Tag(name: 'Enclosures', description: 'Enclosure management endpoints')]
#[OA\Tag(name: 'Personnel', description: 'Personnel management endpoints')]
#[OA\Tag(name: 'Authentication', description: 'Authentication endpoints')]
class ApiCategoriesOpenApi {}
