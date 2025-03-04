<?php

declare(strict_types=1);

namespace App\Templates\Components\Title;

use App\Templates\Components\TwigComponent;
use App\Templates\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'TitleComponent',
    template: 'Components/Title/TitleComponent.html.twig'
)]
class TitleComponent extends TwigComponent
{
    public TitleComponentDto&TwigComponentDtoInterface $data;

    protected static function getComponentName(): string
    {
        return 'TitleComponent';
    }

    public function mount(TitleComponentDto $data): void
    {
        $this->data = $data;
    }

    public function getType(): string
    {
        return $this->data->type->value;
    }
}
