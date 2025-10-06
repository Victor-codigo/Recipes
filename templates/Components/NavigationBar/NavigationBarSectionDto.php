<?php

declare(strict_types=1);

namespace App\Templates\Components\NavigationBar;

class NavigationBarSectionDto
{
    public function __construct(
        public readonly string $label,
        public readonly string $title,
        public readonly string $url,
        public readonly string $icon,
        public readonly bool $active,
    ) {
    }
}
