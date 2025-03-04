<?php

declare(strict_types=1);

namespace App\Templates\Components\List;

use App\Templates\Components\TwigComponentDtoInterface;

class ListComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $itemComponentName,
        public readonly array $listItemsDto,
        public readonly string $listEmptyIconAlt,
        public readonly string $listEmptyMessage,
    ) {
    }
}
