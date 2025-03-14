<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\ItemRemove;

use App\Templates\Components\TwigComponentDtoInterface;

readonly class ItemRemoveComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public string $componentName,
        public array $errors,
        public string $csrfToken,
        public string $formActionUrl,
        public bool $removeMulti,
    ) {
    }
}
