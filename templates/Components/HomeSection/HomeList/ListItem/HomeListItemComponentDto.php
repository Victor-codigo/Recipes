<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\HomeList\ListItem;

use App\Templates\Components\TwigComponentDtoInterface;

class HomeListItemComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $componentName,
        public readonly string $id,
        public readonly string $name,
        public readonly string $modifyFormModalIdAttribute,
        public readonly string $deleteFormModalIdAttribute,

        public readonly string $translationDomainName,
    ) {
    }
}
