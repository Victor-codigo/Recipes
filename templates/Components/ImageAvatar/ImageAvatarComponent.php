<?php

declare(strict_types=1);

namespace App\Templates\Components\ImageAvatar;

use App\Templates\Components\TwigComponent;
use App\Templates\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ImageAvatarComponent',
    template: 'Components/ImageAvatar/ImageAvatarComponent.html.twig'
)]
class ImageAvatarComponent extends TwigComponent
{
    public ImageAvatarComponentDto|TwigComponentDtoInterface $data;

    protected static function getComponentName(): string
    {
        return 'ImageAvatarComponent';
    }

    public function mount(ImageAvatarComponentDto $data): void
    {
        $this->data = new ImageAvatarComponentDto(
            $this->validateImageSrc($data->imageSrc, $data->imageNoAvatar),
            $data->imageNoAvatar,
            $data->imageAlt
        );
    }

    private function validateImageSrc(?string $imageSrc, ?string $imageNoAvatar): string
    {
        if (null === $imageSrc) {
            return $imageNoAvatar;
        }

        return $imageSrc;
    }
}
