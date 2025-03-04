<?php

declare(strict_types=1);

namespace App\Templates\Components\Alert;

use App\Templates\Components\TwigComponentDtoInterface;

class AlertComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly ALERT_TYPE $type,
        public readonly ?string $title,
        public readonly ?string $subtitle,
        public readonly array|string $messages,
        public readonly bool $escapeMessage = true,
        public readonly bool $visible = true,
    ) {
    }
}
