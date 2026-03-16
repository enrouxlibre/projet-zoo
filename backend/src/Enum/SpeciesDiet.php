<?php

declare(strict_types=1);

namespace App\Enum;

enum SpeciesDiet: string
{
    case CARNIVOROUS = 'carnivorous';
    case HERBIVOROUS = 'herbivorous';
    case OMNIVOROUS = 'omnivorous';
}
