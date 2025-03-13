<?php

declare(strict_types=1);

namespace App\Templates\Components\DropZone;

use App\Templates\Components\TwigComponentDtoInterface;

class DropZoneComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly ?string $componentId = null,
        public readonly ?string $labelField = null,
        public readonly ?string $nameField = null,
        public readonly ?string $placeholderField = null,
    ) {
    }
}
