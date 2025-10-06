<?php

declare(strict_types=1);

namespace App\Templates\Components\NavigationBar;

class BackButtonDto
{
    public function __construct(
        public readonly string $url,
        public readonly string $title,
    ) {
    }
}
