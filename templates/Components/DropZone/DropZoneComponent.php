<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\DropZone;

use App\Templates\Components\DropZone\DropZoneComponentDto;
use App\Templates\Components\TwigComponent;
use App\Templates\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'DropZoneComponent',
    template: 'Components/DropZone/DropZoneComponent.html.twig'
)]
class DropZoneComponent extends TwigComponent
{
    public DropZoneComponentDto&TwigComponentDtoInterface $data;

    protected static function getComponentName(): string
    {
        return 'DropZoneComponent';
    }

    public function mount(DropZoneComponentDto $data): void
    {
        $this->data = $data;
    }
}