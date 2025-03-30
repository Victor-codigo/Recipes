<?php

declare(strict_types=1);

namespace App\Templates\Components\Rating;

readonly class RatingComponentDto
{
    public function __construct(
        public int $rating,
    ) {
    }
}
