<?php

declare(strict_types=1);

namespace App\Templates\Components\Modal;

use App\Templates\Components\TwigComponent;
use App\Templates\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ModalComponent',
    template: 'Components/Modal/ModalComponent.html.twig'
)]
class ModalComponent extends TwigComponent
{
    public ModalComponentDto&TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'ModalComponent';
    }

    public function mount(ModalComponentDto&TwigComponentDtoInterface $data): void
    {
        $this->data = $data;
    }
}
