<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\ItemInfo;

use App\Templates\Components\TwigComponentDtoInterface;

class ItemInfoComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $componentName,
    ) {
    }
}
