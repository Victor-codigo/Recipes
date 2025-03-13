<?php

declare(strict_types=1);

namespace App\Templates\Components\AlertValidation;

use App\Templates\Components\TwigComponentDtoInterface;

class AlertValidationComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $messageValidationOk = [],
        public readonly array $messageErrors = [],
        public readonly bool $visible = true,
    ) {
    }
}
