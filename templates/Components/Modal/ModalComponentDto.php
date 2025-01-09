<?php

declare(strict_types=1);

namespace App\Templates\Components\Modal;

use App\Templates\Components\TwigComponentDtoInterface;

class ModalComponentDto implements TwigComponentDtoInterface
{
    /**
     * @param ModalComponentButtonDto[] $buttons
     * */
    public function __construct(
        public readonly string $idAttribute,
        public readonly string $title,
        public readonly bool $closeButton,
        public readonly ?string $contentComponentName,
        public readonly string|TwigComponentDtoInterface $content,

        public readonly array $buttons,
    ) {
    }
}
