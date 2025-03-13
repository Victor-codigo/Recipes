<?php

declare(strict_types=1);

namespace App\Templates\Components\ImageAvatar;

use App\Templates\Components\TwigComponentDtoInterface;

class ImageAvatarComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly ?string $imageSrc = null,
        public readonly ?string $imageNoAvatar = null,
        public readonly ?string $imageAlt = null,
    ) {
    }
}
