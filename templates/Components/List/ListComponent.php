<?php

declare(strict_types=1);

namespace App\Templates\Components\List;

use App\Templates\Components\TwigComponent;
use App\Templates\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ListComponent',
    template: 'Components/List/ListComponent.html.twig'
)]
final class ListComponent extends TwigComponent
{
    public ListComponentDto&TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'ListComponent';
    }

    public function mount(ListComponentDto $data): void
    {
        $this->data = $data;
    }
}
