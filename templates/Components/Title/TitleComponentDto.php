<?php

declare(strict_types=1);

namespace App\Templates\Components\Title;

use App\Templates\Components\TwigComponentDtoInterface;

class TitleComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $titleLabel,
        public readonly TITLE_TYPE $type,
        public readonly ?string $titlePathLabel,
    ) {
    }
}
