<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\HomeList\ListItem;

use App\Templates\Components\TwigComponentDtoInterface;

readonly class HomeListItemComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public string $componentName,
        public string $id,
        public string $name,
        public string $modifyFormModalIdAttribute,
        public string $deleteFormModalIdAttribute,

        public string $translationDomainName,
    ) {
    }
}
