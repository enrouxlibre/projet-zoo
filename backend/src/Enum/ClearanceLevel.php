<?php

declare(strict_types=1);

namespace App\Enum;

enum ClearanceLevel: int
{
    case MINIMAL = 1;
    case LOW = 2;
    case MODERATE = 3;
    case HIGH = 4;
    case CRITICAL = 5;
}
