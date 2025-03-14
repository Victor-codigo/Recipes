<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\ItemInfo;

use App\Templates\Components\TwigComponent;
use App\Templates\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ItemInfoComponent',
    template: 'Components/HomeSection/ItemInfo/ItemInfoComponent.html.twig'
)]
abstract class ItemInfoComponent extends TwigComponent
{
    public readonly ItemInfoComponentLangDto $lang;
    public ItemInfoComponentDto&TwigComponentDtoInterface $data;

    public string $componentName;

    abstract public function mount(ItemInfoComponentDto&TwigComponentDtoInterface $data): void;

    abstract public static function getComponentName(): string;
}
